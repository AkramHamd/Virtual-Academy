// src/components/HeroSection.js
import React from 'react';
import './HeroSection.css';

export default function HeroSection({ onGetStarted }) {
  return (
    <div className="hero-section">
      <h1>Welcome to Virtual Academy</h1>
      <p>Learn from the best, at your own pace, anywhere.</p>
      <button onClick={onGetStarted} className="hero-button">
        Get Started
      </button>
    </div>
  );
}
