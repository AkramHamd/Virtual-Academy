// src/App.js
import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import HomePage from './pages/HomePage';
import LoginPage from './pages/auth/LoginPage';
import RegisterPage from './pages/auth/RegisterPage';
import UserPage from './pages/UserPage';
import LandingPage from './pages/LandingPage';
import CourseCatalogPage from './pages/courses/CourseCatalogPage';
import CourseDetailsPage from './pages/courses/CourseDetailsPage';
import AdminLayout from './layouts/AdminLayout';
import AdminDashboardPage from './pages/admin/AdminDashboardPage';

function App() {
    return (
        <Router>
            <Routes>
                <Route path="/" element={<LandingPage />} />
                <Route path="/register" element={<RegisterPage />} />
                <Route path="/login" element={<LoginPage />} />
                <Route path="/user" element={<UserPage />} />
                <Route path="/courses" element={<CourseCatalogPage />} />
                <Route path="/courses/:id" element={<CourseDetailsPage />} />
                <Route path="/admin/*" element={<AdminLayout />} />
                <Route path="/admin-dashboard" element={<AdminDashboardPage />} />
            </Routes>
        </Router>
    );
}

export default App;
