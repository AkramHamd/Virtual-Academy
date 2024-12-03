import React, { useState, useEffect } from 'react';
import Navbar from '../../components/common/Navbar';
import './AdminDashboardPage.css'; // Importa los estilos específicos de admin
import courseService from '../../services/courseService'; // Asegúrate de importar el servicio

export default function AdminDashboardPage() {
  const [courses, setCourses] = useState([]);
  const [newCourse, setNewCourse] = useState({
    title: '',
    description: '',
    category: '',
    cover_image_url: '',
  });

  const [isCreating, setIsCreating] = useState(false);

  // Cargar los cursos desde el backend usando el servicio
  useEffect(() => {
    const fetchCourses = async () => {
      const coursesData = await courseService.getAllCourses();
      setCourses(coursesData);
    };
    fetchCourses();
  }, []);

  // Manejar cambios en los inputs del formulario
  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setNewCourse({
      ...newCourse,
      [name]: value,
    });
  };

  // Crear un nuevo curso
  const createCourse = () => {
    fetch('http://localhost/Virtual-Academy/backend/api/courses/create_course.php', { // Endpoint para crear el curso
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(newCourse),
      credentials: "include",
    })
      .then((response) => response.json())
      .then((data) => {
        alert(data.message);
        if (data.message === 'Course created successfully.') {
          setCourses([...courses, newCourse]); // Añadir el nuevo curso a la lista de cursos
          setNewCourse({ title: '', description: '', category: '', cover_image_url: '' }); // Limpiar el formulario
        }
      })
      .catch((error) => console.error('Error creating course:', error));
  };

  // Eliminar un curso
  const deleteCourse = (courseId) => {
    fetch('http://localhost/Virtual-Academy/backend/api/courses/delete_course.php', { // Endpoint para eliminar el curso
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ course_id: courseId }),
      credentials: "include",
    })
      .then((response) => response.json())
      .then((data) => {
        alert(data.message);
        if (data.message === 'Course deleted successfully.') {
          setCourses(courses.filter((course) => course.id !== courseId)); // Eliminar el curso de la lista
        }
      })
      .catch((error) => console.error('Error deleting course:', error));
  };

  return (
    <div className="admin-page">
      <Navbar />
      <div className="admin-container">
        {/* Información general del administrador */}
        <section className="admin-info-section">
          <h2>Admin Dashboard</h2>
        </section>

        {/* Botón para gestionar cursos */}
        <section className="admin-actions-section">
          <h2>Actions</h2>
          <button
            className="admin-button"
            onClick={() => setIsCreating(!isCreating)}
          >
            {isCreating ? 'Cancel' : ' Create course'}
          </button>
        </section>

        {/* Mostrar formulario de creación de curso */}
        {isCreating && (
          <section className="admin-create-course-section">
            <h3>Create New Course</h3>
            <div>
              <input
                type="text"
                name="title"
                value={newCourse.title}
                placeholder="Title"
                onChange={handleInputChange}
              />
              <input
                type="text"
                name="category"
                value={newCourse.category}
                placeholder="Category"
                onChange={handleInputChange}
              />
              <textarea
                name="description"
                value={newCourse.description}
                placeholder="Description"
                onChange={handleInputChange}
              />
              <input
                type="text"
                name="cover_image_url"
                value={newCourse.cover_image_url}
                placeholder="Cover Image URL"
                onChange={handleInputChange}
              />
              <button className="admin-button" onClick={createCourse}>
                Create Course
              </button>
            </div>
          </section>
        )}

        {/* Listado de cursos */}
        <section className="admin-course-list">
          <h3>All Courses</h3>
          {courses.length > 0 ? (
            <div className="course-grid">
              {courses.map((course) => (
                <div className="course-item" key={course.id}>
                  <img
                    src={course.cover_image_url}
                    alt={course.title}
                    className="course-cover"
                  />
                  <h4>{course.title}</h4>
                  <p>{course.category}</p>
                  <p>{course.description}</p>
                  <button
                    className="admin-button delete-button"
                    onClick={() => deleteCourse(course.id)}
                  >
                    Delete
                  </button>
                </div>
              ))}
            </div>
          ) : (
            <p>No courses available.</p>
          )}
        </section>
      </div>
    </div>
  );
}
