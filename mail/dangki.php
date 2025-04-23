<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng ký tài khoản</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      height: 100vh;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .form-container {
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width: 300px;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"] {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      width: 100%;
      background-color: #27a4f2;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }

    button:hover {
      background-color: #27a4f2;
    }
  </style>
</head>
<body>

<div class="form-container">
  <h2>Đăng ký tài khoản</h2>
  <form action="send.php" method="POST">
    <label for="ten">Tên:</label>
    <input type="text" name="tenkh" id="ten" required>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>

    <label for="sdt">SDT:</label>
    <input type="tel" name="sdt" id="sdt" required pattern="[0-9]{10,11}">

    <button type="submit">Đăng ký</button>
  </form>
</div>

</body>
</html>