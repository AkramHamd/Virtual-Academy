import axios from 'axios';

const baseURL = 'http://localhost/Virtual-Academy/backend/api/';

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
  },

  // NUEVO MÉTODO para obtener estudiantes de un curso
  getStudentsByCourse: async (courseId) => {
    try {
      const response = await axios.get(
        `${baseURL}courses/get_students_by_course.php`,
        {
          params: { course_id: courseId },
          withCredentials: true
        }
      );
      return response.data;
    } catch (error) {
      console.error("Error fetching students:", error);
      return [];
    }
  },

  createModule: async (moduleData) => {
    try {
      const response = await axios.post(
        `${baseURL}modules/create_module.php`,
        moduleData,
        { withCredentials: true }
      );
      console.log('Module created:', response.data);
      return response.data;
    } catch (error) {
      console.error("Error creating module:", error);
      throw error;
    }
  },

  deleteModule: async (moduleId, courseId) => {
    try {
      const response = await axios.delete(
        `${baseURL}modules/delete_module.php`,
        { 
          data: { 
            module_id: moduleId, 
            course_id: courseId 
          },
          withCredentials: true
        }
      );
      console.log('Module deleted:', response.data);
      return response.data;
    } catch (error) {
      console.error("Error deleting module:", error);
      throw error;
    }
  }
  
};

export default courseService;
