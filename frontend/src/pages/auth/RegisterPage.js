// src/pages/RegisterPage.js
import { useState } from 'react';
import Navbar from '../../components/common/Navbar';

const RegisterPage = () => {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [message, setMessage] = useState('');

  const validateInput = () => {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
      setMessage('Invalid email format.');
      return false;
    }
    if (password.length < 6) {
      setMessage('Password must be at least 6 characters.');
      return false;
    }
    setMessage('');
    return true;
  };

  const handleRegister = async (e) => {
    e.preventDefault();
    if (!validateInput()) return;

    try {
      const response = await fetch('http://localhost/virtual-academy/backend/api/auth/register.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, email, password }),
      });

      const result = await response.json();
      setMessage(response.ok ? 'Registration successful! You can now log in.' : result.message || 'Registration failed.');
    } catch (error) {
      console.error('Error:', error);
      setMessage('An error occurred during registration.');
    }
  };

  return (
    <>
      <Navbar />
      <div className="form-container">
        <h2 className="form-title">Register</h2>
        {message && <p className="message">{message}</p>}
        <form onSubmit={handleRegister}>
          <input
            type="text"
            placeholder="Name"
            value={name}
            onChange={(e) => setName(e.target.value)}
            required
            className="input-field"
          />
          <input
            type="email"
            placeholder="Email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
            className="input-field"
          />
          <input
            type="password"
            placeholder="Password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
            className="input-field"
          />
          <button type="submit" className="button">Register</button>
        </form>
      </div>
    </>
  );
};

export default RegisterPage;
