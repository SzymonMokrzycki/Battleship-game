<script setup>
import { ref, watch } from 'vue'
import AdminDataService from '../../services/AdminDataService'
import { TailwindPagination } from "laravel-vue-pagination";
import useEventsBus from "@/composables/eventBus";
import BanUserModal from "../modals/BanUserModal.vue"
import AddUserModal from '../modals/AddUserModal.vue'

const { emit, bus } = useEventsBus();

const dataContent = ref([])
const pagination = ref({})

let name = ref("")

let filters = {
    name: { value: name, keys: ["name"] },
};

const getUsers = async (page = 1) => {
  return AdminDataService.getUsers(page)
    .then((res) => {
      dataContent.value = res.data.data.data;
      pagination.value = res.data.data.pagination;
    })
    .catch((error) => {
      console.log(error.response.data);
    });
};

getUsers();

watch(
  () => bus.value.get('refreshUsersTable'),
  () => {
    getUsers();
  }
);

async function deleteUser(id) {
  return AdminDataService.deleteUser(id)
    .then(() => {
      getUsers();
    })
    .catch((error) => {
      console.log(error.response.data);
    });
}
</script>

<template>
    <div class="m-auto mt-12 w-48 flex">
        <label for="username" class="block text-sm font-medium text-white"
        >Filter by username:</label
        >
        <div>
        <input
            type="text"
            name="name"
            id="name"
            v-model="name"
            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
            placeholder="Username"
        />
        </div>
    </div>
    <VTable 
    id="tabletwo" 
    sortHeaderClass="flex items-center justify-between w-full" 
    :data="dataContent" 
    :filters="filters" 
    class="mt-2 border-separate border-spacing-2 table-auto rounded text-lg border-2 border-orange-500 m-auto text-black">
    <template #head>
        <tr class="border-2 border-orange-500">
            <VTh 
            sortKey="name"
            scope="col">Username</VTh>
            <VTh
            sortKey="email"
            scope="col">Email</VTh>
            <VTh
            sortKey="role_id"
            scope="col">Role</VTh>
            <VTh
            sortKey="banned_time"
            scope="col">Banned time (days)</VTh>
            <th>Actions</th>
        </tr>
    </template>
    <template #body="{rows}">
        <tr v-for="object in rows" :key="object.id" class="border-2 border-orange-500">
            <td>{{ object.name }}</td>
            <td>{{ object.email }}</td>
            <td v-if="object.role_id == 1">Super admin</td>
            <td v-if="object.role_id == 2">admin forum</td>
            <td v-if="object.role_id == 3">admin game</td>
            <td v-if="object.role_id == 4">user</td>
            <td>{{ object.banned_time }}</td>
            <td>
                <button @click="emit('showBanUserModal', {user_id: object.id})" class="hover:text-white mr-2" v-if="object.role_id == 4" title="Ban user">
                    <font-awesome-icon :icon="['fas', 'ban']" />
                </button>
                <button @click="deleteUser(object.id)" class="hover:text-white" v-if="object.role_id == 4" title="Delete user">
                    <font-awesome-icon :icon="['fas', 'trash']" />
                </button>
            </td>
        </tr>
    </template>
    </VTable>
    <button @click="emit('showAddUserModal')" type="button"
        class="inline-flex items-center py-2.5 px-4 text-xs font-medium text-center text-white bg-primary-700 rounded-lg focus:ring-4 focus:ring-primary-200 dark:focus:ring-primary-900 hover:bg-primary-800">
        Add user
    </button>
    <TailwindPagination 
    class="justify-center text-white bg-transparent"
    :data="pagination"
    @pagination-change-page="getUsers"
    :limit="1"
    />
    <BanUserModal />
    <AddUserModal />
</template>