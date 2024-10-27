// src/App.js
import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import CourseCatalogPage from './pages/courses/CourseCatalogPage';
import HomePage from './pages/HomePage';
import LoginPage from './pages/auth/LoginPage';
import RegisterPage from './pages/auth/RegisterPage';
import UserPage from './pages/UserPage';
import LandingPage  from './pages/LandingPage';

function App() {
    return (
        <Router>
            <Routes>
                <Route path="/" element={<LandingPage />} />
                <Route path="/courses" element={<CourseCatalogPage />} />
                <Route path="/register" element={<RegisterPage />} />
                <Route path="/login" element={<LoginPage />} />
                <Route path="/user" element={<UserPage />} />
            </Routes>
        </Router>
    );
}

export default App;
