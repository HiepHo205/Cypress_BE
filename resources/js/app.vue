<template>
  <div class="admin-layout">
    <aside class="sidebar">
      <h1 class="logo">Cypress</h1>
      <nav>
        <ul>
          <li><router-link to="/admin">Dashboard</router-link></li>
          <li><router-link to="/admin/users">Users</router-link></li>
        </ul>
      </nav>
    </aside>
    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
}
.sidebar {
  width: 250px;
  background-color: #2c3e50;
  color: white;
  padding: 20px;
}
.logo {
  text-align: center;
  margin-bottom: 30px;
}
.main-content {
  flex-grow: 1;
  padding: 20px;
  background-color: #f4f6f9;
}
nav ul { list-style: none; padding: 0; }
nav a { color: #bdc3c7; text-decoration: none; display: block; padding: 10px 15px; border-radius: 4px; }
nav a:hover, .router-link-exact-active { background-color: #34495e; color: white; }
</style>