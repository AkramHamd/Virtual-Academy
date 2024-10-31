// src/pages/courses/CourseCatalogPage.js
import React, { useEffect, useState } from 'react';
import courseService from '../../services/courseService';
import CourseCard from '../../components/cards/CourseCard';
import Navbar from '../../components/common/Navbar';
import { useAuth } from '../../contexts/AuthContext';
import './CourseCatalogPage.css';

export default function CourseCatalogPage() {
  const { user } = useAuth();
  const [courses, setCourses] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchCourses = async () => {
      if (user) { // Only fetch courses if the user is authenticated
        const data = await courseService.getAllCourses();
        if (data) setCourses(data);
      }
      setLoading(false);
    };
    fetchCourses();
  }, [user]);

  return (
    <>
      <Navbar />
      <div className="course-catalog-container">
        <h1 className="catalog-title">Available Courses</h1>
        {loading ? (
          <p className="loading-text">Loading courses...</p>
        ) : !user ? (
          <p className="error-text">Please sign in to view available courses.</p>
        ) : (
          <div className="course-grid">
            {courses.map((course) => (
              <CourseCard key={course.id} course={course} />
            ))}
          </div>
        )}
      </div>
    </>
  );
}
