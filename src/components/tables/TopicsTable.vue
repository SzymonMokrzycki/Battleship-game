<script setup>
import { ref, watch } from 'vue'
import AdminDataService from '../../services/AdminDataService'
import { TailwindPagination } from "laravel-vue-pagination";
import useEventsBus from "@/composables/eventBus";
import EditTopicModal from "../modals/EditTopicModal.vue"

const dataContent = ref([])
const pagination = ref({})
const { emit, bus } = useEventsBus();

const getTopics = async (page = 1) => {
  return AdminDataService.getTopics(page)
    .then((res) => {
      dataContent.value = res.data.data.data;
      pagination.value = res.data.data.pagination;
    })
    .catch((error) => {
      console.log(error.response.data);
    });
};

let name = ref("")
let author = ref("")

let filters = {
    name: { value: name, keys: ["name"] },
    author: { value: author, keys: ["created_by"] },
};

getTopics();

async function deleteTopic(id) {
  return AdminDataService.deleteTopic(id)
    .then(() => {
      getTopics();
    })
    .catch((error) => {
      console.log(error.response.data);
    });
}

watch(
  () => bus.value.get('refreshTopicTable'),
  () => {
    getTopics();
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
        <label for="author" class="block text-sm font-medium text-white"
        >Filter by author:</label
        >
        <div>
        <input
            type="text"
            name="author"
            id="author"
            v-model="author"
            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
            placeholder="Author"
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
            sortKey="created_by"
            scope="col">Created by</VTh>
            <VTh
            sortKey="created_at"
            scope="col">Created at</VTh>
            <th>Actions</th>
        </tr>
    </template>
    <template #body="{rows}">
        <tr v-for="object in rows" :key="object.id" class="border-2 border-orange-500">
            <td>{{ object.name }}</td>
            <td>{{ object.created_by }}</td>
            <td>{{ object.created_at }}</td>
            <td>
                <button @click="emit('showEditTopicModal', {user_id: object.id})" class="hover:text-white mr-2" title="Edit topic">
                    <font-awesome-icon :icon="['fas', 'pen-to-square']" />
                </button>
                <button @click="deleteTopic(object.id)" class="hover:text-white" title="Delete topic">
                    <font-awesome-icon :icon="['fas', 'trash']" />
                </button>
            </td>
        </tr>
    </template>
    </VTable>
    <TailwindPagination 
    class="justify-center text-white bg-transparent"
    :data="pagination"
    @pagination-change-page="getTopics"
    :limit="1"
    />
    <EditTopicModal/>
</template>