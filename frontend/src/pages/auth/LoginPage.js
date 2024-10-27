// src/pages/auth/LoginPage.js
import { useState } from 'react';
import authService from '../../services/authService';
import { Container, Error, Base, Title, Input, Submit, Text, Link } from '../../components/forms/FormStyles';
import Navbar from '../../components/common/Navbar';

export default function LoginPage() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const isInvalid = password === '' || email === '';

  const handleLogin = async (e) => {
    e.preventDefault();
    setError('');

    try {
      const response = await authService.login(email, password);
      if (response && response.message === 'Login successful.') {
        alert('Login Successful');
      } else {
        setError(response?.message || 'Login failed. Please try again.');
      }
    } catch (err) {
      setError('An error occurred. Please try again.');
      console.error('Login error:', err);
    }
  };

  return (
    <>
      <Navbar />
      <Container>
        <Title>Sign In</Title>
        {error && <Error>{error}</Error>}
        <Base onSubmit={handleLogin} method="POST">
          <Input
            type="email"
            placeholder="Email address"
            value={email}
            onChange={({ target }) => setEmail(target.value)}
          />
          <Input
            type="password"
            placeholder="Password"
            autoComplete="off"
            value={password}
            onChange={({ target }) => setPassword(target.value)}
          />
          <Submit disabled={isInvalid} type="submit">Sign In</Submit>
        </Base>
        <Text>
          New to our platform? <Link to="/register">Sign up now.</Link>
        </Text>
      </Container>
    </>
  );
}
