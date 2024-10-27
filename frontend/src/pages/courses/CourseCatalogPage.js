// import { useEffect, useState } from 'react';
// import courseService from '../../services/courseService';
// // import CourseCard from '../../components/CourseCard';

// export default function CourseCatalogPage() {
//   const [courses, setCourses] = useState([]);

//   useEffect(() => {
//     const fetchCourses = async () => {
//       const data = await courseService.getAllCourses();
//       setCourses(data);
//     };
//     fetchCourses();
//   }, []);

//   return (
//     <div className="course-catalog">
//       <h1>Course Catalog</h1>
//       <div className="course-list">
//         {courses.map(course => (
//           <CourseCard key={course.id} course={course} />
//         ))}
//       </div>
//     </div>
//   );
// }
