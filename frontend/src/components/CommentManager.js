import React, { useState, useEffect } from "react";
import {
  getCommentsByCourse,
  addComment,
  updateComment,
  deleteComment,
} from "../services/commentService";
import courseService from "../services/courseService";
import authService from "../services/authService";
import "./CommentManager.css"; 

const CommentManager = ({ courseId }) => {
  const [comments, setComments] = useState([]);
  const [students, setStudents] = useState([]);
  const [newComment, setNewComment] = useState({ comment: "", rating: "", user_id: "" });
  const [editingComment, setEditingComment] = useState(null);
  const [isLoading, setIsLoading] = useState(false);
  const [user, setUser] = useState(null);


  useEffect(() => {
    fetchComments();
    fetchStudents();
    const fetchUserInfo = async () => {
      try {
        const userData = await authService.getUserInfo();
        setUser(userData);
      } catch (error) {
        console.error("Error fetching user info:", error);
      }
    };
    fetchUserInfo();
  }, [courseId]);

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

  const fetchStudents = async () => {
    try {
      const data = await courseService.getStudentsByCourse(courseId);
      setStudents(data);
    } catch (error) {
      console.error("Error fetching students:", error);
    }
  };

  const handleAddComment = async () => {
    // Verificar que el comentario, la calificación y el estudiante están presentes
    if (!newComment.comment.trim() || !newComment.rating) {
      alert("Please fill in all fields.");
      return;
    }
    if (Number(newComment.rating) < 1 || Number(newComment.rating) > 10) {
      alert("Rating must be between 1 and 10.");
      return;
    }

    try {
      // Asegurarse de que el studentId correcto se está pasando a la API
      const response = await addComment({
        ...newComment,
        user_id: user.id, // Asignar el ID del usuario autenticado
        course_id: courseId,
      });
      alert(response.message);
      setNewComment({ comment: "", rating: "" }); // Limpiar formulario
      fetchComments();
    } catch (error) {
      console.error("Error adding comment:", error);
    }
  };

  const handleEditClick = (comment) => {
    setEditingComment(comment);
  };

  const handleUpdateComment = async () => {
    try {
      const response = await updateComment({
        comment_id: editingComment.id,
        comment: editingComment.comment,
        rating: editingComment.rating,
        user_id: editingComment.user_id,
      });
      alert(response.message);
      setEditingComment(null);
      fetchComments();
    } catch (error) {
      console.error("Error updating comment:", error);
    }
  };

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
    <div className="comment-manager">
      <h3>Comments for Course {courseId}</h3>
      {isLoading ? (
        <p>Loading comments...</p>
      ) : (
        <>
            <div className="add-comment-section">
              
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

          <ul className="comments-list">
            {comments.map((comment) => (
              <li key={comment.id} className="comment-item">
                {editingComment && editingComment.id === comment.id ? (
                  <div className="edit-comment">
                    <p>
                      Student:{" "}
                      {students.find(student => student.id === editingComment.user_id)?.name || "Unknown"}
                    </p>
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
                  <div className="comment-view">
                    <p>{comment.comment}</p>
                    <p>by: {comment.nameuser}</p>
                    <p>Rating: {comment.rating}</p>
                    {user?.role === "admin" && (
                      <button onClick={() => handleEditClick(comment)}>Edit</button>
                    )}
                    {user?.role === "admin" && (
                      <button onClick={() => handleDeleteComment(comment.id)}>Delete</button>
                    )}
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
