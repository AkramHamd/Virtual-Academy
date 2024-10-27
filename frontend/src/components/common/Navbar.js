import React, { useContext } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { AuthContext } from '../../contexts/AuthContext';
import authService from '../../services/authService';
import logo from '../../assets/images/logo.svg';
import './NavbarStyles.css';

export default function Navbar() {
  const navigate = useNavigate();
  const { isAuthenticated, setIsAuthenticated } = useContext(AuthContext);

  const handleLogout = async () => {
    await authService.logout();
    setIsAuthenticated(false); // Update authentication state
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
        {isAuthenticated ? (
          <>
            <button onClick={handleLogout}>Logout</button>
            <img src="path/to/profile-image.jpg" alt="Profile" className="profile-image" />
          </>
        ) : (
          <Link to="/login">Log In</Link>
        )}
      </nav>
    </header>
  );
}
