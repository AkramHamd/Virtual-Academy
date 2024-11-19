import React, { useState, useEffect } from "react";
import {
  getCommentsByCourse,
  addComment,
  updateComment,
  deleteComment,
} from "../services/commentService";
import courseService from "../services/courseService"; // Importas el objeto completo

const CommentManager = ({ courseId }) => {
  const [comments, setComments] = useState([]);
  const [students, setStudents] = useState([]); // Nuevo estado para los estudiantes
  const [newComment, setNewComment] = useState({ comment: "", rating: "", studentId: "" });
  const [editingComment, setEditingComment] = useState(null); // Nuevo estado para manejar la edición
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    fetchComments();
    fetchStudents(); // Cargar estudiantes cuando cambia el courseId
  }, [courseId]);

  // Obtener comentarios
  const fetchComments = async () => {
    setIsLoading(true);
    try {
      const data = await getCommentsByCourse(courseId);
      setComments(data);
    } catch (error) {
      console.error("Error fetching comments:", error);
    } finally {
      setIsLoading(false);
    }
  };

  // Obtener estudiantes
  const fetchStudents = async () => {
    try {
      const data = await courseService.getStudentsByCourse(courseId); // Uso de la función desde el objeto importado
      setStudents(data);
    } catch (error) {
      console.error("Error fetching students:", error);
    }
  };

  // Agregar comentario
  const handleAddComment = async () => {
    if (!newComment.comment.trim() || !newComment.rating || !newComment.studentId) {
      alert("Please fill in all fields.");
      return;
    }
    if (newComment.rating < 1 || newComment.rating > 10) {
      alert("Rating must be between 1 and 10.");
      return;
    }

    try {
      const response = await addComment({
        ...newComment,
        course_id: courseId,
      });
      alert(response.message);
      setNewComment({ comment: "", rating: "", studentId: "" }); // Reset form
      fetchComments();
    } catch (error) {
      console.error("Error adding comment:", error);
    }
  };

  // Iniciar edición de comentario
  const handleEditClick = (comment) => {
    setEditingComment(comment); // Establece el comentario que se está editando
  };

  // Guardar cambios de edición
  const handleUpdateComment = async () => {
    if (!editingComment.comment.trim() || !editingComment.rating) {
      alert("Please fill in all fields.");
      return;
    }
    if (editingComment.rating < 1 || editingComment.rating > 10) {
      alert("Rating must be between 1 and 10.");
      return;
    }

    try {
      const response = await updateComment({
        comment_id: editingComment.id,
        comment: editingComment.comment,
        rating: editingComment.rating,
      });
      alert(response.message);
      setEditingComment(null); // Finaliza la edición
      fetchComments();
    } catch (error) {
      console.error("Error updating comment:", error);
    }
  };

  // Eliminar comentario
  const handleDeleteComment = async (commentId) => {
    const confirmDelete = window.confirm("Are you sure you want to delete this comment?");
    if (!confirmDelete) return;

    try {
      const response = await deleteComment(commentId);
      alert(response.message);
      fetchComments();
    } catch (error) {
      console.error("Error deleting comment:", error);
    }
  };

  return (
    <div className="manage-comments">
      <h3>Comments for Course {courseId}</h3>
      {isLoading ? (
        <p>Loading comments...</p>
      ) : (
        <>
          <div className="add-comment-section">
            <select
              value={newComment.studentId}
              onChange={(e) =>
                setNewComment((prev) => ({ ...prev, studentId: e.target.value }))
              }
            >
              <option value="">Select Student</option>
              {students.map((student) => (
                <option key={student.id} value={student.id}>
                  {student.name}
                </option>
              ))}
            </select>
            <input
              type="text"
              placeholder="Write a comment..."
              value={newComment.comment}
              onChange={(e) =>
                setNewComment((prev) => ({ ...prev, comment: e.target.value }))
              }
            />
            <input
              type="number"
              placeholder="Rating (1-10)"
              value={newComment.rating}
              onChange={(e) =>
                setNewComment((prev) => ({ ...prev, rating: e.target.value }))
              }
            />
            <button onClick={handleAddComment}>Add Comment</button>
          </div>

          <ul>
            {comments.map((comment) => (
              <li key={comment.id}>
                {editingComment && editingComment.id === comment.id ? (
                  <div>
                    <textarea
                      value={editingComment.comment}
                      onChange={(e) =>
                        setEditingComment((prev) => ({
                          ...prev,
                          comment: e.target.value,
                        }))
                      }
                    />
                    <input
                      type="number"
                      value={editingComment.rating}
                      onChange={(e) =>
                        setEditingComment((prev) => ({
                          ...prev,
                          rating: e.target.value,
                        }))
                      }
                    />
                    <button onClick={handleUpdateComment}>Save</button>
                    <button onClick={() => setEditingComment(null)}>Cancel</button>
                  </div>
                ) : (
                  <div>
                    <p>
                      {comment.comment} (Rating: {comment.rating}) for {comment.studentName}
                    </p>
                    <button onClick={() => handleEditClick(comment)}>Edit</button>
                    <button onClick={() => handleDeleteComment(comment.id)}>Delete</button>
                  </div>
                )}
              </li>
            ))}
          </ul>
        </>
      )}
    </div>
  );
};

export default CommentManager;
