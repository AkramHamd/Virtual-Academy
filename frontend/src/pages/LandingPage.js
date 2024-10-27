// src/pages/LandingPage.js
import React from 'react';
import { useNavigate } from 'react-router-dom';
import HeroSection from '../components/cards/HeroSection';

export default function LandingPage() {
  const navigate = useNavigate();

  const handleGetStarted = () => {
    const isAuthenticated = !!localStorage.getItem('authToken'); // Simple check for authentication
    if (isAuthenticated) {
      navigate('/courses'); // Redirect to courses if authenticated
    } else {
      navigate('/login'); // Redirect to login if not authenticated
    }
  };

  return (
    <div>
      <HeroSection onGetStarted={handleGetStarted} />
    </div>
  );
}
