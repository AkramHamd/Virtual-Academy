import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import courseService from '../../services/courseService';
import authService from '../../services/authService';
import Navbar from '../../components/common/Navbar';
import './CourseDetailsPage.css';
import CommentManager from '../../components/CommentManager'; // Importar el componente para gestionar comentarios

export default function CourseDetailsPage() {
  const { id } = useParams();
  const [course, setCourse] = useState(null);
  const [modules, setModules] = useState([]);
  const [currentModule, setCurrentModule] = useState(null);
  const [newModule, setNewModule] = useState({ course_id: id, title: '', video_url: '', support_material_url: '' }); 
  const [showCreateModuleForm, setShowCreateModuleForm] = useState(false); 
  const [isEnrolled, setIsEnrolled] = useState(false);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [showComments, setShowComments] = useState(false); 
  const [isEditing, setIsEditing] = useState(false); 
  const [editedComment, setEditedComment] = useState(''); 
  const [commentIdToEdit, setCommentIdToEdit] = useState(null); 
  const [user, setUser] = useState(null);

  useEffect(() => {
    const fetchCourseData = async () => {
      try {
        const courseData = await courseService.getCourseById(id);
        setCourse(courseData);

        const enrolledCourses = await courseService.getEnrolledCourses();
        const enrolled = enrolledCourses.some(course => course.id === parseInt(id));
        setIsEnrolled(enrolled);

        if (enrolled) {
          const modulesData = await courseService.getCourseModules(id);
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
    const fetchUserInfo = async () => {
      try {
        const userData = await authService.getUserInfo();
        setUser(userData);
      } catch (error) {
        console.error("Error fetching user info:", error);
      }
    };
    fetchUserInfo();
    fetchCourseData();
  }, [id]);

  const handleCreateModule = async () => {
    try {
      const response = await courseService.createModule(newModule);
      if (response && response.module_id) {
        const newModuleData = {
          id: response.module_id,
          title: newModule.title, 
          video_url: newModule.video_url, 
          support_material_url: newModule.support_material_url,
        };
  
        setModules((prevModules) => [...prevModules, newModuleData]);
        setCurrentModule(newModuleData);
        setShowCreateModuleForm(false);
        setNewModule({ course_id: id, title: '', video_url: '', support_material_url: '' });
      } else {
        throw new Error('Invalid response from server');
      }
    } catch (error) {
      console.error('Error creating module:', error);
    }
  };
  
  

  const handleDeleteModule = async (moduleId) => { 
    try {
      // Ahora pasamos tanto moduleId como courseId
      await courseService.deleteModule(moduleId, id);  // `id` es el course_id
      setModules((prevModules) => prevModules.filter((module) => module.id !== moduleId));
    } catch (error) {
      console.error('Error deleting module:', error);
    }
  };  

  const handleModuleSelect = (module) => {
    setCurrentModule(module);
  };

  const handleEnroll = async () => {
    try {
      const response = await courseService.enrollInCourse(id);
      if (response.message === "Enrollment successful.") {
        setIsEnrolled(true);
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

  const handleEditComment = (comment) => {
    setIsEditing(true);
    setEditedComment(comment.text);
    setCommentIdToEdit(comment.id);
  };

  const handleConfirmEdit = async () => {
    try {
      await courseService.updateComment(commentIdToEdit, editedComment); // Llamada al servicio para actualizar el comentario
      setIsEditing(false);
    } catch (error) {
      console.error('Error updating comment:', error);
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
                    {currentModule.support_material_url && (
                      <button
                        className="support-material-btn"
                        onClick={() => window.open(currentModule.support_material_url, '_blank')}
                      >
                        View Support Material
                      </button>
                    )}
                </>
              )}
            </div>

            <div className="modules-list">
              <h3>Course Modules</h3>
              {/* Verificar si modules tiene datos antes de mapear */}
              {modules && modules.length > 0 ? (
                modules.map((module, index) => (
                  <div
                    key={module.id}
                    className={`module-item ${currentModule?.id === module.id ? 'active' : ''}`}
                    onClick={() => handleModuleSelect(module)}
                  >
                    <span className="module-number">{index + 1}</span>
                    <div className="module-info">
                      <h4>{module.title}</h4>
                    </div>
                    {user?.role === "admin" && (
                      <button onClick={() => handleDeleteModule(module.id)} className="delete-module-btn">
                        Delete
                      </button>
                    )}
                  </div>
                ))
              ) : (
                <div>No modules available</div>
              )}
            </div>

            {/* Botón para ver/ocultar los comentarios */}
            <div className="toggle-comments-btn-container">
              <button onClick={() => setShowComments(!showComments)} className="toggle-comments-btn">
                {showComments ? 'Hide Comments' : 'View Comments'}
              </button>
            </div>
             {/* Botón para crear un módulo */}
             {user?.role === "admin" && (
              <div className="add-module-btn-container">
                <button 
                  onClick={() => setShowCreateModuleForm(!showCreateModuleForm)} 
                  className="create-module-btn"
                >
                  {showCreateModuleForm ? 'Cancelar' : 'Añadir Módulo'}
                </button>
              </div>
              )}

              {user?.role === "admin" && showCreateModuleForm && (
              <div className="create-module-form">
                <input
                  type="text"
                  placeholder="Module Title"
                  value={newModule.title}
                  onChange={(e) => setNewModule({ ...newModule, title: e.target.value })}
                />
                <input
                  type="text"
                  placeholder="Video URL"
                  value={newModule.video_url}
                  onChange={(e) => setNewModule({ ...newModule, video_url: e.target.value })}
                />
                <input
                  type="text"
                  placeholder="Support Material URL"
                  value={newModule.support_material_url}
                  onChange={(e) => setNewModule({ ...newModule, support_material_url: e.target.value })}
                />
                <button onClick={handleCreateModule}>Add Module</button>
              </div>
            )}

            {/* Mostrar los comentarios si está activado el estado */}
            <div className={`comments-container ${showComments ? 'show' : ''}`}>
              {showComments && (
                <CommentManager
                  courseId={id}
                  onEditComment={handleEditComment} // Función para editar un comentario
                />
              )}
            </div>

            {/* Formulario para editar el comentario */}
            {isEditing && (
              <div className="edit-comment">
                <textarea
                  value={editedComment}
                  onChange={(e) => setEditedComment(e.target.value)}
                  placeholder="Edit your comment"
                />
                <button onClick={handleConfirmEdit}>Confirm Changes</button>
              </div>
            )}
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
