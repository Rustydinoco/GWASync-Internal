<script setup>
defineProps({
  users: Object
})
</script>

<template>
  <div class="overflow-x-auto">
    <table class="min-w-full border-collapse border">
      <thead>
        <tr>
          <th class="border p-2">NIA</th>
          <th class="border p-2">Nama</th>
          <th class="border p-2">Email</th>
          <th class="border p-2">Role</th>
          <th class="border p-2">No. HP</th>
          <th class="border p-2">QR Code KTA</th>
          <th class="border p-2">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users.data" :key="user.id">
          <td class="border p-2">{{ user.nia }}</td>
          <td class="border p-2">{{ user.name }}</td>
          <td class="border p-2">{{ user.email }}</td>
          <td class="border p-2">{{ user.role }}</td>
          <td class="border p-2">{{ user.phone }}</td>
          <td class="border p-2">{{ user.kta_qr_code }}</td>
          <td class="border p-2">
            <button class="text-blue-500 hover:underline">Edit</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>