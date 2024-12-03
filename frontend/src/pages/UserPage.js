import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom'; // Importa el hook useNavigate
import authService from '../services/authService';
import courseService from '../services/courseService';
import Navbar from '../components/common/Navbar';
import CourseCard from '../components/cards/CourseCard';
import './UserPage.css';

export default function UserPage() {
  const [user, setUser] = useState(null);
  const [enrolledCourses, setEnrolledCourses] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const navigate = useNavigate(); // Inicializa el hook useNavigate

  useEffect(() => {
    const fetchUserData = async () => {
      try {
        const response = await authService.getUserInfo();
        console.log('User info response:', response);

        if (response?.id) {
          setUser(response);
          const coursesResponse = await courseService.getEnrolledCourses(response.id);
          console.log('Enrolled courses response:', coursesResponse);
          setEnrolledCourses(Array.isArray(coursesResponse) ? coursesResponse : []);
        } else {
          setError('Failed to fetch user info. Please log in.');
        }
      } catch (err) {
        setError('An error occurred while fetching user info.');
        console.error('User fetch error:', err);
      } finally {
        setLoading(false);
      }
    };
    fetchUserData();
  }, []);

  const handleAdminClick = () => {
    navigate('/admin-dashboard'); // Redirige directamente a AdminDashboard
  };

  if (loading) return <p>Loading user info...</p>;
  if (error) return <p className="error-message">{error}</p>;

  return (
    <div className="user-page">
      <Navbar />
      <div className="user-container">
        {/* User Info Section */}
        <section className="user-info-section">
          <h2>User Profile</h2>
          {user && (
            <div className="user-details">
              <p><strong>Name:</strong> {user.name}</p>
              <p><strong>Email:</strong> {user.email}</p>
              <p><strong>Role:</strong> {user.role === 'admin' ? 'Admin' : 'Student'}</p>
              {user.role === 'admin' && (
                <button 
                  className="admin-button" 
                  onClick={handleAdminClick} // Usa la función handleAdminClick para redirigir
                >
                  Admin Page
                </button>
              )}
            </div>
          )}
        </section>

        {/* Enrolled Courses Section */}
        <section className="enrolled-courses-section">
          <h2>My Courses</h2>
          {enrolledCourses.length > 0 ? (
            <div className="courses-grid">
              {enrolledCourses.map(course => (
                <CourseCard 
                  key={course.id} 
                  course={course} 
                  isEnrolled={true} 
                />
              ))}
            </div>
          ) : (
            <p className="no-courses-message">You haven't enrolled in any courses yet.</p>
          )}
        </section>
      </div>
    </div>
  );
}
