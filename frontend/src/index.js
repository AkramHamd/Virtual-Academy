import React from 'react';
import ReactDOM from 'react-dom/client'; // Make sure this import is correct
import './assets/css/index.css';
import App from './App';
import { AuthProvider } from './contexts/AuthContext';

// Get the root element and create a root for React 18
const rootElement = document.getElementById('root');
const root = ReactDOM.createRoot(rootElement);

// Use the new render method for React 18
root.render(
    <React.StrictMode>
        <AuthProvider>
            <App />
        </AuthProvider>
    </React.StrictMode>
);
