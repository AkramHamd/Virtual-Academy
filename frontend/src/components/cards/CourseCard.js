// src/components/cards/CourseCard.js
import React from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import './CourseCard.css';
import defaultCover from '../../assets/images/default-cover.jpg'; // Add a default cover image here

export default function CourseCard({ course }) {
  const { user } = useAuth();
  const navigate = useNavigate();

  const handlePlay = () => {
    if (user) {
      navigate(`/courses/${course.id}`);
    } else {
      navigate('/login');
    }
  };

  return (
    <div className="course-card">
      <img
        src={course.cover_image_url || defaultCover} // Use defaultCover if cover_image_url is null
        alt={`${course.title} Cover`}
        className="course-cover"
      />
      <div className="course-details">
        <h3>{course.title}</h3>
        <button onClick={handlePlay} className="play-button">Enroll</button>
      </div>
    </div>
  );
}
