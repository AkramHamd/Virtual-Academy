<<<<<<< Updated upstream
const API_URL = "http://localhost/backend/api/comments";
=======
const API_URL = "http://localhost/Virtual-Academy/backend/api/comments";
>>>>>>> Stashed changes

// Obtener comentarios por curso
export const getCommentsByCourse = async (courseId) => {
  const response = await fetch(`${API_URL}/get_comments_by_course.php?course_id=${courseId}`);
  return response.json();
};

// Añadir un comentario
export const addComment = async (commentData) => {
  const response = await fetch(`${API_URL}/add_comment.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(commentData),
    credentials: "include", // Para enviar cookies de sesión
  });
  return response.json();
};

// Actualizar un comentario
export const updateComment = async (commentData) => {
  const response = await fetch(`${API_URL}/update_comments.php`, {
    method: "PUT",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(commentData),
    credentials: "include",
  });
  return response.json();
};

// Eliminar un comentario
export const deleteComment = async (commentId) => {
  const response = await fetch(`${API_URL}/delete_comments.php`, {
    method: "DELETE",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ comment_id: commentId }),
    credentials: "include",
  });
  return response.json();
};
