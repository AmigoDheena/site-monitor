/**
 * Site Monitor Application
 * Professional Dashboard JavaScript
 */

class SiteMonitor {
    constructor() {
        this.config = {
            apiUrl: 'api.php',
            checkInterval: 300000, // 5 minutes
            autoRefresh: true,
            toastDuration: 5000
        };
        
        this.state = {
            sites: [],
            isLoggedIn: false,
            currentUser: null,
            autoRefreshInterval: null
        };
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.checkAuthStatus();
    }
    
    bindEvents() {
        // Login form
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', (e) => this.handleLogin(e));
        }
        
        // Add site form
        const addSiteForm = document.getElementById('addSiteForm');
        if (addSiteForm) {
            addSiteForm.addEventListener('submit', (e) => this.handleAddSite(e));
        }
        
        // Page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible' && this.state.isLoggedIn) {
                this.loadSites();
            }
        });
        
        // Window unload cleanup
        window.addEventListener('beforeunload', () => {
            this.cleanup();
        });
    }
    
    async checkAuthStatus() {
        try {
            const response = await this.apiCall('me');
            if (response.success) {
                this.state.currentUser = response.user;
                this.state.isLoggedIn = true;
                this.showDashboard();
                return;
            }
        } catch (error) {
            console.log('Not authenticated');
        }
        this.showLogin();
    }
    
    async handleLogin(e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const loginBtn = e.target.querySelector('button[type="submit"]');
        
        this.setLoading(loginBtn, true);
        
        try {
            const response = await this.apiCall('login', 'POST', {
                username: username,
                password: password,
                remember_me: false
            });
            
            if (response.success) {
                this.state.currentUser = response.user;
                this.state.isLoggedIn = true;
                this.showDashboard();
                this.showToast('Welcome back!', 'success');
            } else {
                this.showAlert('loginAlert', response.message || 'Login failed!', 'danger');
            }
        } catch (error) {
            this.showAlert('loginAlert', 'Connection error. Please try again.', 'danger');
        } finally {
            this.setLoading(loginBtn, false);
        }
    }
    
    async logout() {
        try {
            await this.apiCall('logout', 'POST');
        } catch (error) {
            console.error('Logout error:', error);
        }
        
        this.state.isLoggedIn = false;
        this.state.currentUser = null;
        this.cleanup();
        this.showLogin();
        this.showToast('Logged out successfully', 'info');
    }
    
    showLogin() {
        document.getElementById('loginPage').classList.remove('is-hidden');
        document.getElementById('dashboard').classList.add('is-hidden');
    }
    
    showDashboard() {
        document.getElementById('loginPage').classList.add('is-hidden');
        document.getElementById('dashboard').classList.remove('is-hidden');
        this.loadSites();
        this.startAutoRefresh();
        this.updateUserInfo();
    }
    
    updateUserInfo() {
        const userDisplay = document.getElementById('userDisplay');
        if (userDisplay && this.state.currentUser) {
            userDisplay.textContent = this.state.currentUser.username;
        }
    }
    
    async handleAddSite(e) {
        e.preventDefault();
        
        const name = document.getElementById('siteName').value.trim();
        const url = document.getElementById('siteUrl').value.trim();
        const submitBtn = e.target.querySelector('button[type="submit"]');
        
        if (!name || !url) {
            this.showToast('Please fill in all fields!', 'warning');
            return;
        }
        
        this.setLoading(submitBtn, true);
        
        try {
            const response = await this.apiCall('sites', 'POST', {
                name: name,
                url: url,
                check_interval: 300
            });
            
            if (response.success) {
                this.loadSites();
                document.getElementById('addSiteForm').reset();
                this.showToast('Site added successfully!', 'success');
                
                // Check the new site immediately
                if (response.site) {
                    setTimeout(() => this.checkSiteStatus(response.site.id), 1000);
                }
            } else {
                this.showToast(response.message || 'Failed to add site!', 'danger');
            }
        } catch (error) {
            this.showToast('Connection error. Please try again.', 'danger');
        } finally {
            this.setLoading(submitBtn, false);
        }
    }
    
    async deleteSite(siteId) {
        if (!confirm('Are you sure you want to delete this site?')) {
            return;
        }
        
        try {
            const response = await this.apiCall(`site&id=${siteId}`, 'DELETE');
            
            if (response.success) {
                this.loadSites();
                this.showToast('Site deleted successfully!', 'success');
            } else {
                this.showToast(response.message || 'Failed to delete site!', 'danger');
            }
        } catch (error) {
            this.showToast('Connection error. Please try again.', 'danger');
        }
    }
    
    async editSite(siteId) {
        const site = this.state.sites.find(s => s.id == siteId);
        if (!site) return;
        
        // Create a modal or use prompts (for now using prompts)
        const newName = prompt('Enter new site name:', site.name);
        const newUrl = prompt('Enter new site URL:', site.url);
        
        if (newName && newUrl) {
            try {
                const response = await this.apiCall(`site&id=${siteId}`, 'PUT', {
                    name: newName.trim(),
                    url: newUrl.trim()
                });
                
                if (response.success) {
                    this.loadSites();
                    this.showToast('Site updated successfully!', 'success');
                } else {
                    this.showToast(response.message || 'Failed to update site!', 'danger');
                }
            } catch (error) {
                this.showToast('Connection error. Please try again.', 'danger');
            }
        }
    }
    
    async checkSiteStatus(siteId) {
        try {
            const response = await this.apiCall(`check-site&id=${siteId}`, 'POST');
            
            if (response.success) {
                this.loadSites();
                this.showToast('Site checked successfully!', 'info');
            }
            
            return response;
        } catch (error) {
            console.error('Error checking site status:', error);
            this.showToast('Error checking site status', 'danger');
            return { success: false, error: error.message };
        }
    }
    
    async checkAllSites() {
        const button = document.getElementById('checkAllBtn');
        if (!button) return;
        
        this.setLoading(button, true, 'Checking...');
        
        try {
            const response = await this.apiCall('check-all', 'POST');
            
            if (response.success) {
                this.loadSites();
                this.showToast('All sites checked successfully!', 'success');
            } else {
                this.showToast('Failed to check all sites!', 'danger');
            }
        } catch (error) {
            this.showToast('Connection error while checking sites.', 'danger');
        } finally {
            this.setLoading(button, false, 'Check All Sites');
        }
    }
    
    async loadSites() {
        try {
            const response = await this.apiCall('sites');
            
            if (response.success) {
                this.state.sites = response.sites;
                this.updateStats(response.stats);
                this.renderSitesList();
            } else {
                console.error('Failed to load sites:', response);
            }
        } catch (error) {
            console.error('Error loading sites:', error);
        }
    }
    
    renderSitesList() {
        const sitesList = document.getElementById('sitesList');
        if (!sitesList) return;
        
        if (this.state.sites.length === 0) {
            sitesList.innerHTML = this.getEmptyStateHTML();
            return;
        }
        
        sitesList.innerHTML = this.state.sites.map(site => this.getSiteItemHTML(site)).join('');
    }
    
    getEmptyStateHTML() {
        return `
            <div class="empty-state">
                <div class="empty-state-icon">🌐</div>
                <div class="empty-state-title">No sites added yet</div>
                <div class="empty-state-description">Add your first website to start monitoring</div>
            </div>
        `;
    }
    
    getSiteItemHTML(site) {
        const statusInfo = this.getStatusInfo(site.status);
        const lastChecked = site.last_checked 
            ? new Date(site.last_checked).toLocaleString() 
            : 'Never checked';
        
        return `
            <div class="site-item">
                <div class="site-header">
                    <div class="site-info">
                        <div class="site-name">${this.escapeHtml(site.name)}</div>
                        <div class="site-url">${this.escapeHtml(site.url)}</div>
                    </div>
                    <div class="site-actions">
                        <button class="button is-small is-primary" onclick="app.checkSiteStatus(${site.id})">
                            <span class="icon is-small">
                                <i class="fas fa-sync-alt"></i>
                            </span>
                            <span>Check</span>
                        </button>
                        <button class="button is-small" onclick="app.editSite(${site.id})">
                            <span class="icon is-small">
                                <i class="fas fa-edit"></i>
                            </span>
                            <span>Edit</span>
                        </button>
                        <button class="button is-small is-danger" onclick="app.deleteSite(${site.id})">
                            <span class="icon is-small">
                                <i class="fas fa-trash"></i>
                            </span>
                            <span>Delete</span>
                        </button>
                    </div>
                </div>
                
                <div class="site-status-row">
                    <div class="status-indicator">
                        <span class="status-dot ${statusInfo.dotClass}"></span>
                        <span class="status-tag ${statusInfo.tagClass}">${statusInfo.text}</span>
                    </div>
                    
                    ${site.response_time > 0 ? `
                        <div class="metric-item">
                            <span class="metric-value">${site.response_time}ms</span> response time
                        </div>
                    ` : ''}
                    
                    ${site.status_code > 0 ? `
                        <div class="metric-item">
                            HTTP <span class="metric-value">${site.status_code}</span>
                        </div>
                    ` : ''}
                    
                    ${site.uptime_percentage !== undefined ? `
                        <div class="metric-item">
                            <span class="metric-value">${site.uptime_percentage}%</span> uptime
                        </div>
                    ` : ''}
                </div>
                
                <div class="metric-item">
                    <i class="fas fa-clock"></i> Last checked: ${lastChecked}
                </div>
                
                ${site.error_message ? `
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        ${this.escapeHtml(site.error_message)}
                    </div>
                ` : ''}
            </div>
        `;
    }
    
    getStatusInfo(status) {
        const statusMap = {
            'online': {
                text: 'ONLINE',
                dotClass: 'is-online',
                tagClass: 'is-online'
            },
            'offline': {
                text: 'OFFLINE',
                dotClass: 'is-offline',
                tagClass: 'is-offline'
            },
            'warning': {
                text: 'WARNING',
                dotClass: 'is-warning',
                tagClass: 'is-warning'
            },
            'checking': {
                text: 'CHECKING',
                dotClass: 'is-checking',
                tagClass: 'is-checking'
            },
            'pending': {
                text: 'PENDING',
                dotClass: 'is-checking',
                tagClass: 'is-checking'
            }
        };
        
        return statusMap[status] || statusMap['pending'];
    }
    
    updateStats(stats = null) {
        if (!stats) {
            // Calculate from current sites if no stats provided
            const totalSites = this.state.sites.length;
            const onlineSites = this.state.sites.filter(site => site.status === 'online').length;
            const offlineSites = this.state.sites.filter(site => site.status === 'offline').length;
            
            stats = {
                total_sites: totalSites,
                online_sites: onlineSites,
                offline_sites: offlineSites
            };
        }
        
        this.setElementText('totalSites', stats.total_sites);
        this.setElementText('onlineSites', stats.online_sites);
        this.setElementText('offlineSites', stats.offline_sites);
        
        // Update warning sites if available
        if (stats.warning_sites !== undefined) {
            this.setElementText('warningSites', stats.warning_sites);
        }
    }
    
    startAutoRefresh() {
        if (this.state.autoRefreshInterval) {
            clearInterval(this.state.autoRefreshInterval);
        }
        
        if (this.config.autoRefresh) {
            this.state.autoRefreshInterval = setInterval(() => {
                this.loadSites();
            }, this.config.checkInterval);
        }
    }
    
    cleanup() {
        if (this.state.autoRefreshInterval) {
            clearInterval(this.state.autoRefreshInterval);
            this.state.autoRefreshInterval = null;
        }
    }
    
    // Utility methods
    async apiCall(endpoint, method = 'GET', data = null) {
        const url = `${this.config.apiUrl}?action=${endpoint}`;
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            }
        };
        
        if (data && method !== 'GET') {
            options.body = JSON.stringify(data);
        }
        
        try {
            const response = await fetch(url, options);
            const result = await response.json();
            
            // For login endpoint, don't throw error on 401 if we got a JSON response
            if (endpoint === 'login' && response.status === 401 && result) {
                return result;
            }
            
            // For other endpoints, throw error on non-ok status
            if (!response.ok) {
                throw new Error(result.message || `HTTP error! status: ${response.status}`);
            }
            
            return result;
        } catch (error) {
            // If it's a network error or JSON parsing error
            if (error.name === 'TypeError' || error.name === 'SyntaxError') {
                throw new Error('Connection error. Please check your internet connection.');
            }
            throw error;
        }
    }
    
    setLoading(element, isLoading, loadingText = 'Loading...') {
        if (!element) return;
        
        if (isLoading) {
            element.disabled = true;
            element.classList.add('is-loading');
            if (loadingText && element.textContent) {
                element.dataset.originalText = element.textContent;
                element.textContent = loadingText;
            }
        } else {
            element.disabled = false;
            element.classList.remove('is-loading');
            if (element.dataset.originalText) {
                element.textContent = element.dataset.originalText;
                delete element.dataset.originalText;
            }
        }
    }
    
    setElementText(id, text) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = text;
        }
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    showAlert(elementId, message, type) {
        const alertElement = document.getElementById(elementId);
        if (!alertElement) return;
        
        const typeClass = type === 'danger' ? 'is-danger' : 
                         type === 'success' ? 'is-success' : 
                         type === 'warning' ? 'is-warning' : 'is-info';
        
        alertElement.innerHTML = `
            <div class="notification ${typeClass}">
                <button class="delete"></button>
                ${this.escapeHtml(message)}
            </div>
        `;
        
        // Add delete functionality
        const deleteBtn = alertElement.querySelector('.delete');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => {
                alertElement.innerHTML = '';
            });
        }
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            alertElement.innerHTML = '';
        }, this.config.toastDuration);
    }
    
    showToast(message, type = 'info') {
        const container = this.getToastContainer();
        const toast = document.createElement('div');
        toast.className = `notification ${this.getToastClass(type)} toast`;
        
        toast.innerHTML = `
            <button class="delete"></button>
            ${this.escapeHtml(message)}
        `;
        
        container.appendChild(toast);
        
        // Add delete functionality
        const deleteBtn = toast.querySelector('.delete');
        deleteBtn.addEventListener('click', () => {
            container.removeChild(toast);
        });
        
        // Auto-remove after duration
        setTimeout(() => {
            if (container.contains(toast)) {
                container.removeChild(toast);
            }
        }, this.config.toastDuration);
    }
    
    getToastContainer() {
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        return container;
    }
    
    getToastClass(type) {
        const classMap = {
            'success': 'is-success',
            'danger': 'is-danger',
            'warning': 'is-warning',
            'info': 'is-info'
        };
        return classMap[type] || 'is-info';
    }
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.app = new SiteMonitor();
});
