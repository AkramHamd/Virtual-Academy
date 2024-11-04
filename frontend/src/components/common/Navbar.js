// src/components/common/Navbar.js
import React from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import './NavbarStyles.css';

export default function Navbar() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  return (
    <header className="navbar">
      <div className="navbar-container">
        <div className="navbar-logo">
          <Link to="/">Virtual Academy</Link>
        </div>
        
        <nav className="navbar-links">
          <ul>
            <li>
              <Link 
                to="/courses" 
                className={location.pathname === '/courses' ? 'active' : ''}
              >
                <span className="nav-icon">📚</span>
                My Learning
              </Link>
            </li>
          </ul>
        </nav>

        <nav className="navbar-actions">
          {user && (
            <>
              <Link 
                to="/user" 
                className={`profile-link ${location.pathname === '/user' ? 'active' : ''}`}
              >
                <span className="nav-icon">👤</span>
                My Profile
              </Link>
              <button onClick={handleLogout} className="logout-btn">
                Logout
              </button>
            </>
          )}
        </nav>
      </div>
    </header>
  );
}
