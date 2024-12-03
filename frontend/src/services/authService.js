import axios from 'axios';

const baseURL = 'http://localhost/Virtual-Academy/backend/api/auth/';

const authService = {
  login: async (email, password) => {
    try {
      const response = await axios.post(`${baseURL}login.php`, { email, password }, { withCredentials: true });
      return response.data;
    } catch (error) {
      console.error("Login error:", error);
      return null;
    }
  },

  getUserInfo: async () => {
    try {
      const response = await axios.get(`${baseURL}/user.php`, { withCredentials: true });
      return response.data;
    } catch (error) {
      console.error('Error fetching user info:', error);
      return null;
    }
  },

  logout: async () => {
    try {
      const response = await axios.post(`${baseURL}logout.php`, {}, { withCredentials: true });
      return response.data;
    } catch (error) {
      console.error("Logout error:", error);
      return null;
    }
  }
};

export default authService;
