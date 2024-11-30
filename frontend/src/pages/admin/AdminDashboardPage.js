import React from 'react';
import './AdminDashboardPage.css'; // Archivo CSS con los estilos específicos

const AdminDashboardPage = () => {
  return (
    <div className="admin-dashboard">
      <h2>Admin Dashboard</h2>
      <div className="dashboard-content">
        <p>Welcome to the Admin Panel. Manage courses, comments, and more from here.</p>
        <div className="admin-actions">
          <button className="admin-button">Manage Users</button>
          <button className="admin-button">Manage Courses</button>
          <button className="admin-button">View Reports</button>
        </div>
      </div>
    </div>
  );
};

export default AdminDashboardPage;
