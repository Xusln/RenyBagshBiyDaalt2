// screens/BookAddScreen.js
import React, { useState } from 'react';
import { View, ScrollView, Alert } from 'react-native';
import { TextInput, Button, Text, HelperText, Dropdown } from 'react-native-paper';
import axios from 'axios';

export default function BookAddScreen() {
  const [form, setForm] = useState({
    title: '',
    author: '',
    publication_year: '',
    isbn: '',
    publisher: '',
    binding_type: '',
    pages: '',
    language: '',
    price: '',
    category: '',
    subject: '',
    location: '',
    total_copies: '',
    available_copies: '',
    ordered_by: '',
    description: '',
    status: '',
    edition: ''
  });

  const handleChange = (name, value) => {
    setForm(prev => ({ ...prev, [name]: value }));
  };

  const submitBook = async () => {
    try {
      const response = await axios.post('http://YOUR_SERVER_IP/add_book.php', form);
      Alert.alert('Амжилттай', 'Ном нэмэгдлээ');
    } catch (err) {
      Alert.alert('Алдаа', 'Ном нэмэх үед алдаа гарлаа');
    }
  };

  return (
    <ScrollView style={{ padding: 16 }}>
      <Text variant="headlineMedium" style={{ marginBottom: 16 }}>📚 Ном нэмэх</Text>
      {[
        ['title', 'Гарчиг'],
        ['author', 'Зохиогч'],
        ['publication_year', 'Он'],
        ['isbn', 'ISBN'],
        ['publisher', 'Хэвлэлийн газар'],
        ['binding_type', 'Хийц'],
        ['pages', 'Хуудасны тоо'],
        ['language', 'Хэл'],
        ['price', 'Үнэ'],
        ['category', 'Категори'],
        ['subject', 'Сэдэв'],
        ['location', 'Байршил'],
        ['total_copies', 'Нийт хувь'],
        ['available_copies', 'Чөлөөт хувь'],
        ['ordered_by', 'Захиалсан хүн'],
        ['description', 'Тайлбар'],
        ['status', 'Статус'],
        ['edition', 'Хэвлэл']
      ].map(([key, label]) => (
        <TextInput
          key={key}
          label={label}
          value={form[key]}
          onChangeText={(value) => handleChange(key, value)}
          style={{ marginBottom: 12 }}
        />
      ))}
      <Button mode="contained" onPress={submitBook}>Ном нэмэх</Button>
    </ScrollView>
  );
}
