import React from 'react';
import { Route, Routes } from 'react-router-dom';
import Sidebar from '../components/common/Sidebar';
import ManageComments from '../pages/admin/ManageComments';
import AdminDashboardPage from '../pages/admin/AdminDashboardPage';
import './AdminLayout.css';

const AdminLayout = () => {
  return (
    <div className="admin-layout" style={{ display: 'flex' }}>
      <Sidebar />
      <div className="content" style={{ flex: 1, padding: '20px' }}>
        <Routes>
          {/* Ruta principal del dashboard */}
          <Route path="/" element={<AdminDashboardPage />} />
          <Route path="comments" element={<ManageComments />} />
        </Routes>
      </div>
    </div>
  );
};

export default AdminLayout;
