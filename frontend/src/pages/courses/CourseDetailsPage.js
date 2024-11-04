import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import courseService from '../../services/courseService';
import Navbar from '../../components/common/Navbar';
import './CourseDetailsPage.css';

export default function CourseDetailsPage() {
  const { id } = useParams();
  const [course, setCourse] = useState(null);
  const [modules, setModules] = useState([]);
  const [currentModule, setCurrentModule] = useState(null);
  const [isEnrolled, setIsEnrolled] = useState(false);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    const fetchCourseData = async () => {
      try {
        const courseData = await courseService.getCourseById(id);
        console.log('Course data:', courseData);
        setCourse(courseData);

        const enrolledCourses = await courseService.getEnrolledCourses();
        console.log('Enrolled courses:', enrolledCourses);
        const enrolled = enrolledCourses.some(course => course.id === parseInt(id));
        setIsEnrolled(enrolled);

        if (enrolled) {
          const modulesData = await courseService.getCourseModules(id);
          console.log('Modules data:', modulesData);
          setModules(modulesData);
          if (modulesData.length > 0) {
            setCurrentModule(modulesData[0]);
          }
        }
      } catch (err) {
        console.error('Error:', err);
        setError('Failed to load course data');
      } finally {
        setLoading(false);
      }
    };

    fetchCourseData();
  }, [id]);

  const handleModuleSelect = (module) => {
    setCurrentModule(module);
  };

  const handleEnroll = async () => {
    try {
      const response = await courseService.enrollInCourse(id);
      if (response.message === "Enrollment successful.") {
        setIsEnrolled(true);
        // Fetch modules after successful enrollment
        const modulesData = await courseService.getCourseModules(id);
        setModules(modulesData);
        if (modulesData.length > 0) {
          setCurrentModule(modulesData[0]);
        }
      }
    } catch (error) {
      console.error("Enrollment error:", error);
    }
  };

  if (loading) return <div className="loading">Loading course details...</div>;
  if (error) return <div className="error-message">{error}</div>;
  if (!course) return <div className="error-message">Course not found</div>;

  return (
    <div className="course-details-page">
      <Navbar />
      <div className="course-container">
        <div className="course-header">
          <h1>{course.title}</h1>
          <p>{course.description}</p>
        </div>

        {isEnrolled ? (
          <div className="course-content">
            <div className="video-section">
              {currentModule && (
                <>
                  <h2>{currentModule.title}</h2>
                  <div className="video-container">
                    {currentModule.video_url ? (
                      <iframe
                        width="100%"
                        height="100%"
                        src={`https://www.youtube.com/embed/${getYoutubeId(currentModule.video_url)}`}
                        title={currentModule.title}
                        frameBorder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowFullScreen
                      ></iframe>
                    ) : (
                      <div className="no-video-message">No video available for this module</div>
                    )}
                  </div>
                </>
              )}
            </div>

            <div className="modules-list">
              <h3>Course Modules</h3>
              {modules.map((module, index) => (
                <div
                  key={module.id}
                  className={`module-item ${currentModule?.id === module.id ? 'active' : ''}`}
                  onClick={() => handleModuleSelect(module)}
                >
                  <span className="module-number">{index + 1}</span>
                  <div className="module-info">
                    <h4>{module.title}</h4>
                  </div>
                </div>
              ))}
            </div>
          </div>
        ) : (
          <div className="enroll-section">
            <button onClick={handleEnroll} className="enroll-button">
              Enroll in Course
            </button>
          </div>
        )}
      </div>
    </div>
  );
}

// Helper function to extract YouTube video ID
function getYoutubeId(url) {
  if (!url) return null;
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
  const match = url.match(regExp);
  return (match && match[2].length === 11) ? match[2] : null;
}
