/**
 * Base Path Detection Helper
 * Auto-detect if running on local /pweb-project or live mangrovetour.gt.tc
 * 
 * This file MUST be loaded before any other scripts that use apiUrl()
 */

/**
 * Auto-detect base path
 */
function getBasePath() {
    const currentPath = window.location.pathname;
    if (currentPath.includes('/pweb-project/')) {
        return '/pweb-project';
    }
    return '';
}

// Global constants
const APP_BASE_PATH = getBasePath();

/**
 * Helper function to construct API URLs
 * Works on both local and live environments
 */
function apiUrl(path) {
    return APP_BASE_PATH + path;
}

// Debug: Log current environment
console.log('Environment detected: ' + (APP_BASE_PATH ? 'LOCAL (/pweb-project)' : 'LIVE (domain root)'));
console.log('Base path: "' + APP_BASE_PATH + '"');
