<script setup>
import { ref } from 'vue'
import AdminDataService from '../../services/AdminDataService'
import { TailwindPagination } from "laravel-vue-pagination";

const dataContent = ref([])
const pagination = ref({})

let name = ref("")
let world = ref("")

let filters = {
    name: { value: name, keys: ["name"] },
    world: { value: world, keys: ["world"] },
};

const getIslands = async (page = 1) => {
  return AdminDataService.getIslands(page)
    .then((res) => {
      dataContent.value = res.data.data.data;
      pagination.value = res.data.data.pagination;
    })
    .catch((error) => {
      console.log(error.response.data);
    });
};

getIslands();

async function deleteIsland(id) {
  return AdminDataService.deleteIsland(id)
    .then(() => {
      getIslands();
    })
    .catch((error) => {
      console.log(error.response.data);
    });
}
</script>

<template>
    <div class="m-auto mt-12 w-fit flex">
        <label for="author" class="block text-sm font-medium text-white"
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
        <label for="world" class="block text-sm font-medium text-white"
        >Filter by world:</label
        >
        <div>
        <input
            type="text"
            name="world"
            id="world"
            v-model="world"
            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
            placeholder="World"
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
            sortKey="world"
            scope="col">World</VTh>
            <th>Actions</th>
        </tr>
    </template>
    <template #body="{rows}">
        <tr v-for="object in rows" :key="object.id" class="border-2 border-orange-500">
            <td>{{ object.name }}</td>
            <td>{{ object.world }}</td>
            <td>
                <button @click="deleteIsland(object.id)" class="hover:text-white" title="Delete island">
                    <font-awesome-icon :icon="['fas', 'trash']" />
                </button>
            </td>
        </tr>
    </template>
    </VTable>
    <TailwindPagination 
    class="justify-center text-white bg-transparent"
    :data="pagination"
    @pagination-change-page="getIslands"
    :limit="1"
    />
</template>