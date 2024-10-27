import { Link, useNavigate } from 'react-router-dom';
import authService from '../../services/authService';

export default function Navbar() {
  const navigate = useNavigate();

  const handleLogout = async () => {
    const response = await authService.logout();
    if (response && response.message === 'Logged out successfully.') {
      alert('You have been logged out.');
      navigate('/login');  // Redirect to the login page
    } else {
      alert('Logout failed.');
    }
  };

  return (
    <nav className="navbar">
      <Link to="/">Home</Link>
      <Link to="/courses">Courses</Link>
      <Link to="/profile">Profile</Link>
      <button onClick={handleLogout} className="logout-button">
        Logout
      </button>
    </nav>
  );
}
