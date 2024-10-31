import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import courseService from '../../services/courseService';
import './CourseDetailsPage.css';

export default function CourseDetailsPage() {
  const { id } = useParams();
  const [course, setCourse] = useState(null);

  useEffect(() => {
    const fetchCourse = async () => {
      const data = await courseService.getCourseDetails(id);
      setCourse(data);
    };
    fetchCourse();
  }, [id]);

  if (!course) return <p>Loading course details...</p>;

  return (
    <div className="course-details-page">
      <h1>{course.title}</h1>
      <p>{course.description}</p>
      <button>Enroll Now</button>
    </div>
  );
}
