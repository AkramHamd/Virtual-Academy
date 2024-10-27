// src/pages/UserPage.js
import { useState, useEffect } from 'react';
import authService from '../services/authService';
import Navbar from '../components/common/Navbar';

export default function UserPage() {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    const fetchUser = async () => {
      try {
        const response = await authService.getUserInfo();
        response?.id ? setUser(response) : setError('Failed to fetch user info. Please log in.');
      } catch (err) {
        setError('An error occurred while fetching user info.');
        console.error('User fetch error:', err);
      } finally {
        setLoading(false);
      }
    };
    fetchUser();
  }, []);

  if (loading) return <p>Loading user info...</p>;
  if (error) return <p className="error-message">{error}</p>;

  return (
    <div>
      <Navbar />
      <div className="form-container">
        <h2 className="form-title">User Info</h2>
        {user ? (
          <div>
            <p><strong>Name:</strong> {user.name}</p>
            <p><strong>Email:</strong> {user.email}</p>
            <p><strong>Role:</strong> {user.role === 'admin' ? 'Admin' : 'Student'}</p>
          </div>
        ) : (
          <p>No user data available.</p>
        )}
      </div>
    </div>
  );
}
