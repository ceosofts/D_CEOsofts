/**
 * API Service Utility
 * Standardized methods for API communication
 */

class ApiService {
    /**
     * Perform a GET request
     * @param {string} url - API endpoint
     * @param {object} params - Query parameters
     * @returns {Promise} - Axios promise
     */
    static async get(url, params = {}) {
        try {
            const response = await axios.get(url, { params });
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    }

    /**
     * Perform a POST request
     * @param {string} url - API endpoint
     * @param {object} data - Request payload
     * @returns {Promise} - Axios promise
     */
    static async post(url, data = {}) {
        try {
            const response = await axios.post(url, data);
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    }

    /**
     * Perform a PUT request
     * @param {string} url - API endpoint
     * @param {object} data - Request payload
     * @returns {Promise} - Axios promise
     */
    static async put(url, data = {}) {
        try {
            const response = await axios.put(url, data);
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    }

    /**
     * Perform a DELETE request
     * @param {string} url - API endpoint
     * @param {object} params - Query parameters
     * @returns {Promise} - Axios promise
     */
    static async delete(url, params = {}) {
        try {
            const response = await axios.delete(url, { params });
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    }

    /**
     * Handle API errors with custom logic
     * @param {Error} error - The caught error
     */
    static handleError(error) {
        // Custom error handling beyond the global interceptor
        if (error.response && error.response.data.message) {
            // Display the error message to user (could use a toast notification system)
            console.error('API Error Message:', error.response.data.message);
        }
        
        // Log to analytics or monitoring service if needed
    }
    
    /**
     * Download a file from the API
     * @param {string} url - API endpoint
     * @param {object} params - Query parameters
     * @param {string} filename - Name to save the file as
     */
    static async downloadFile(url, params = {}, filename = 'download') {
        try {
            const response = await axios.get(url, { 
                params, 
                responseType: 'blob'
            });
            
            // Create a download link and trigger it
            const blob = new Blob([response.data]);
            const downloadLink = document.createElement('a');
            downloadLink.href = URL.createObjectURL(blob);
            downloadLink.download = filename;
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
            
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    }
}

// Export the service
export default ApiService;
