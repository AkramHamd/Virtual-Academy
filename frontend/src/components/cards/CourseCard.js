// src/components/cards/CourseCard.js
import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import courseService from '../../services/courseService';
import './CourseCard.css';

export default function CourseCard({ course, isEnrolled = false }) {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [enrollMessage, setEnrollMessage] = useState('');

  const handleEnroll = async () => {
    if (!user) {
      setEnrollMessage('Please log in to enroll.');
      return;
    }

    try {
      const response = await courseService.enrollInCourse(course.id);
      setEnrollMessage(response.message);

      if (response.message === 'Enrollment successful.') {
        navigate(`/courses/${course.id}`);
      }
    } catch (error) {
      console.error("Error enrolling in course:", error);
      setEnrollMessage('Enrollment failed. Please try again.');
    }
  };

  const handleAccess = () => {
    navigate(`/courses/${course.id}`);
  };

  if (!course) return null;

  return (
    <div className="course-card">
      {course.cover_image_url && (
        <img 
          src={course.cover_image_url} 
          alt={`${course.title} Cover`} 
          className="course-cover" 
        />
      )}
      <div className="course-details">
        <h3>{course.title}</h3>
        <h4>{course.category}</h4>
        <p>{course.description}</p>
        {isEnrolled ? (
          <button 
            onClick={handleAccess}
            className="access-button"
          >
            Access Course
          </button>
        ) : (
          <button onClick={handleEnroll}>Enroll</button>
        )}
        {enrollMessage && <p className="enroll-message">{enrollMessage}</p>}
      </div>
    </div>
  );
}

/*
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
                </div>
              ))}
            </div>
          ) : (
            <p>No courses available.</p>
          )}
        </section>
*/
