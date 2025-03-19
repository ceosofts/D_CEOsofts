/**
 * Notifications handling module
 */
export default {
  init() {
    console.log('Notifications module initialized');
  },
  
  show(message, type = 'info') {
    // Display notification
    console.log(`${type}: ${message}`);
  }
};
