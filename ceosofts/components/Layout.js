import React from 'react';
import Navbar from './Navbar'; // Adjust the import path based on your actual Navbar component location

const Layout = ({ children }) => {
  return (
    <div className="app-container">
      <Navbar />
      <main className="main-content">
        {children}
      </main>
    </div>
  );
};

export default Layout;
