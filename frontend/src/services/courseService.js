// src/services/courseService.js
import axios from 'axios';

const baseURL = 'http://localhost/virtual-academy/backend/api/';

const courseService = {
  getAllCourses: async () => {
    try {
      const response = await axios.get(`${baseURL}courses/get_courses.php`, { withCredentials: true });
      console.log("Response data:", response.data);
      return response.data;
    } catch (error) {
      console.error("Error fetching courses:", error);
      return [];
    }
  },
  
  getEnrolledCourses: async () => {
    try {
      const response = await axios.get(
        `${baseURL}enrollments/get_enrolled_courses.php`,
        { withCredentials: true }
      );
      console.log('Enrolled courses API response:', response.data);
      return response.data;
    } catch (error) {
      console.error("Error fetching enrolled courses:", error);
      return [];
    }
  },

  enrollInCourse: async (courseId) => {
    try {
      const response = await axios.post(
        `${baseURL}enrollments/enroll.php`,
        { course_id: courseId },
        { withCredentials: true }
      );
      return response.data;
    } catch (error) {
      console.error("Enrollment error:", error);
      return { message: "Enrollment failed. Please try again." };
    }
  },

  getCourseById: async (courseId) => {
    try {
      const response = await axios.get(
        `${baseURL}courses/get_course_by_id.php`,
        { 
          params: { id: courseId },
          withCredentials: true 
        }
      );
      console.log("Course response:", response.data);
      return response.data;
    } catch (error) {
      console.error("Error fetching course details:", error);
      throw error;
    }
  },

  getCourseModules: async (courseId) => {
    try {
      const response = await axios.get(
        `${baseURL}courses/get_course_modules.php`,
        { 
          params: { course_id: courseId },
          withCredentials: true 
        }
      );
      return response.data;
    } catch (error) {
      console.error("Error fetching course modules:", error);
      return [];
    }
  }
};

export default courseService;

