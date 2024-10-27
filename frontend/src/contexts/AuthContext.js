// src/contexts/AuthContext.js
import React, { createContext, useContext, useEffect, useState } from 'react';
import authService from '../services/authService';

export const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);

  useEffect(() => {
    const checkAuthStatus = async () => {
      // Fetch user info if session is valid
      const userData = await authService.getUserInfo();
      if (userData && userData.id) {
        setUser(userData);
      }
    };
    checkAuthStatus();
  }, []);

  const login = async (email, password) => {
    const data = await authService.login(email, password);
    if (data && data.message === "Login successful.") {
      const userData = await authService.getUserInfo();
      setUser(userData);
    }
  };

  const logout = async () => {
    await authService.logout();
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
};

// Custom hook for accessing AuthContext
export const useAuth = () => useContext(AuthContext);
