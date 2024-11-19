import React from 'react';
import { Route, Routes } from 'react-router-dom';
import Sidebar from '../components/common/Sidebar'; // Importa tu Sidebar
import ManageComments from '../pages/admin/ManageComments'; // Componente de gestión de comentarios
import './AdminLayout.css';

const AdminLayout = () => {
  return (
    <div className="admin-layout" style={{ display: 'flex' }}>
      {/* Sidebar siempre visible */}
      <Sidebar />

      {/* Contenido dinámico */}
      <div className="content" style={{ flex: 1, padding: '20px' }}>
        <Routes>
          <Route path="comments" element={<ManageComments />} />
          {/* Puedes agregar otras rutas en el futuro */}
        </Routes>
      </div>
    </div>
  );
};

export default AdminLayout;
