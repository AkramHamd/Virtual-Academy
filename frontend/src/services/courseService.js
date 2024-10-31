// src/services/courseService.js
import axios from 'axios';

const baseURL = 'http://localhost/virtual-academy/backend/api/courses/';

const courseService = {
  // src/services/courseService.js
  getAllCourses: async () => {
    try {
      const response = await axios.get(`${baseURL}get_courses.php`, { withCredentials: true });
      console.log("Response data:", response.data); // Debugging line
      return response.data;
    } catch (error) {
      console.error("Error fetching courses:", error);
      return [];
    }
  },

  getCourseById: async (id) => {
    try {
      const response = await axios.get(`${baseURL}get_course_by_id.php`, { params: { id }, withCredentials: true });
      return response.data;
    } catch (error) {
      console.error("Error fetching course:", error);
      return null;
    }
  }
};

export default courseService;
