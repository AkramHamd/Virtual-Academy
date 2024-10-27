// src/components/forms/FormStyles.js
import styled from 'styled-components';
import { Link as ReactRouterLink } from 'react-router-dom';

export const Container = styled.div`
  display: flex;
  flex-direction: column;
  min-height: 600px;
  background-color: rgba(0, 0, 0, 0.75);
  border-radius: 5px;
  width: 100%;
  max-width: 450px;
  margin: auto;
  padding: 60px 68px 40px;
  margin-top: 80px;
`;

export const Error = styled.div`
  background: #e87c03;
  border-radius: 4px;
  color: white;
  margin-bottom: 16px;
  padding: 15px;
  font-size: 14px;
  text-align: center;
`;

export const Base = styled.form`
  display: flex;
  flex-direction: column;
  width: 100%;
`;

export const Title = styled.h1`
  color: #fff;
  font-size: 32px;
  font-weight: bold;
  margin-bottom: 28px;
`;

export const Text = styled.p`
  color: #737373;
  font-size: 16px;
`;

export const Link = styled(ReactRouterLink)`
  color: #fff;
  text-decoration: none;
  &:hover {
    text-decoration: underline;
  }
`;

export const Input = styled.input`
  background: #333;
  border-radius: 4px;
  border: none;
  color: white;
  padding: 15px;
  margin-bottom: 20px;
  font-size: 16px;

  &:last-of-type {
    margin-bottom: 30px;
  }
`;

export const Submit = styled.button`
  background: #e50914;
  border-radius: 4px;
  font-size: 16px;
  font-weight: bold;
  padding: 16px;
  color: white;
  border: none;
  cursor: pointer;
  &:disabled {
    opacity: 0.5;
  }
`;
