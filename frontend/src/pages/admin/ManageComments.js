import React, { useState, useEffect } from 'react';
import axios from 'axios';

const ManageComments = ({ courseId }) => {
  const [comments, setComments] = useState([]);
  const [newComment, setNewComment] = useState('');

  // Obtener los comentarios del curso al cargar el componente
  useEffect(() => {
    const fetchComments = async () => {
      try {
        const response = await axios.get(`http://localhost/backend/api/comments.php?course_id=${courseId}`);
        setComments(response.data);
      } catch (error) {
        console.error('Error fetching comments:', error);
      }
    };

    fetchComments();
  }, [courseId]);

  // Agregar un nuevo comentario
  const handleAddComment = async () => {
    if (!newComment) {
      alert('Por favor, ingresa un comentario');
      return;
    }

    try {
      const response = await axios.post('http://localhost/backend/api/comments.php', { comment: newComment, course_id: courseId });
      setComments([...comments, response.data]);
      setNewComment('');
      alert('Comentario agregado con éxito');
    } catch (error) {
      console.error('Error adding comment:', error);
      alert('Hubo un error al agregar el comentario');
    }
  };

  // Eliminar un comentario
  const handleDeleteComment = async (id) => {
    try {
      await axios.delete(`http://localhost/backend/api/comments.php?id=${id}`);
      setComments(comments.filter(comment => comment.id !== id));
      alert('Comentario eliminado con éxito');
    } catch (error) {
      console.error('Error deleting comment:', error);
      alert('Hubo un error al eliminar el comentario');
    }
  };

  // Editar un comentario
  const handleEditComment = async (id) => {
    const updatedComment = prompt('Editar comentario:');
    if (!updatedComment) {
      alert('Por favor, ingresa un nuevo comentario');
      return;
    }

    try {
      const response = await axios.put(`http://localhost/backend/api/comments.php?id=${id}`, { comment: updatedComment });
      setComments(comments.map(comment => (comment.id === id ? response.data : comment)));
      alert('Comentario actualizado con éxito');
    } catch (error) {
      console.error('Error editing comment:', error);
      alert('Hubo un error al actualizar el comentario');
    }
  };

  return (
    <div>
      <h3>Comentarios</h3>
      <div>
        <textarea 
          value={newComment} 
          onChange={(e) => setNewComment(e.target.value)} 
          placeholder="Escribe tu comentario..." 
        />
        <button onClick={handleAddComment}>Agregar Comentario</button>
      </div>

      <ul>
        {comments.map(comment => (
          <li key={comment.id}>
            {comment.text}
            <button onClick={() => handleEditComment(comment.id)}>Editar</button>
            <button onClick={() => handleDeleteComment(comment.id)}>Eliminar</button>
          </li>
        ))}
      </ul>
    </div>
  );
};

export default ManageComments;
