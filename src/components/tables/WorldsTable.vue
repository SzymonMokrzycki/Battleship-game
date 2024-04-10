<script setup>
import { ref, watch } from 'vue'
import AdminDataService from '../../services/AdminDataService'
import { TailwindPagination } from "laravel-vue-pagination";
import useEventsBus from "@/composables/eventBus";
import EditWorldModal from '../modals/EditWorldModal.vue';
import AddWorldModalVue from '../modals/AddWorldModal.vue';

const { emit, bus } = useEventsBus();

const dataContent = ref([])
const pagination = ref({})

let name = ref("")
let status = ref("")

let filters = {
    name: { value: name, keys: ["name"] },
    status: { value: status, keys: ["status"] },
};

const getWorlds = async (page = 1) => {
  return AdminDataService.getWorlds(page)
    .then((res) => {
      dataContent.value = res.data.data.data;
      pagination.value = res.data.data.pagination;
    })
    .catch((error) => {
      console.log(error.response.data);
    });
};

getWorlds();

watch(
  () => bus.value.get('refreshWorldsTable'),
  () => {
    getWorlds();
  }
);
</script>

<template>
    <div class="m-auto mt-12 w-fit flex">
        <label for="name" class="block text-sm font-medium text-white"
        >Filter by name:</label
        >
        <div>
        <input
            type="text"
            name="name"
            id="name"
            v-model="name"
            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
            placeholder="Name"
        />
        </div>
        <label for="name" class="block text-sm font-medium text-white"
        >Filter by status:</label
        >
        <div>
        <input
            type="text"
            name="status"
            id="status"
            v-model="status"
            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
            placeholder="Status"
        />
        </div>
    </div>
    <VTable 
    id="tabletwo" 
    sortHeaderClass="flex items-center justify-between w-full" 
    :data="dataContent" 
    :filters="filters"  
    class="mt-2 border-separate border-spacing-2 rounded text-lg border-2 border-orange-500 m-auto text-black">
    <template #head>
        <tr class="border-2 border-orange-500">
            <VTh
            sortKey="name"
            scope="col">Name</VTh>
            <VTh
            sortKey="status"
            scope="col">Status</VTh>
            <VTh
            sortKey="age"
            scope="col">Age</VTh>
            <VTh
            sortKey="players"
            scope="col">Players</VTh>
            <th>Actions</th>
        </tr>
    </template>
    <template #body="{rows}">
        <tr v-for="object in rows" :key="object.id" class="border-2 border-orange-500">
            <td>{{ object.name }}</td>
            <td>{{ object.status }}</td>
            <td>{{ object.age }}</td>
            <td>{{ object.players }}</td>
            <td>
                <button @click="emit('showEditWorldModal', {user_id:object.id})" class="hover:text-white mr-2" title="Edit post">
                    <font-awesome-icon :icon="['fas', 'pen-to-square']" />
                </button>
            </td>
        </tr>
    </template>
    </VTable>
    <button @click="emit('showAddWorldModal')" type="button"
        class="inline-flex items-center py-2.5 px-4 text-xs font-medium text-center text-white bg-primary-700 rounded-lg focus:ring-4 focus:ring-primary-200 dark:focus:ring-primary-900 hover:bg-primary-800">
        Add world
    </button>
    <TailwindPagination 
    class="justify-center text-white bg-transparent"
    :data="pagination"
    @pagination-change-page="getWorlds"
    :limit="1"
    />
    <EditWorldModal/>
    <AddWorldModalVue/>
</template>

