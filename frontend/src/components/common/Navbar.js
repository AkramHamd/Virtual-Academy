// src/components/common/Navbar.js
import React from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import './NavbarStyles.css';

export default function Navbar() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  return (
    <header className="navbar">
      <div className="navbar-logo">
        <Link to="/">Virtual Academy</Link>
      </div>
      <nav className="navbar-links">
        <ul>
          <li><Link to="/courses">Courses</Link></li>
          <li><Link to="/dashboard">Dashboard</Link></li>
        </ul>
      </nav>
      <nav className="navbar-actions">
        {user ? (
          <>
            <button onClick={handleLogout}>Logout</button>
            <li><Link to="/user">My Account</Link></li>
          </>
        ) : (
          <Link to="/login">Log In</Link>
        )}
      </nav>
    </header>
  );
}
