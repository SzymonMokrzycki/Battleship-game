<script setup>
import { ref, watch } from 'vue'
import AdminDataService from '../../services/AdminDataService'
import { TailwindPagination } from "laravel-vue-pagination";
import useEventsBus from "@/composables/eventBus";
import EditPostModal from "../modals/EditPostModal.vue"

const dataContent = ref([])
const pagination = ref({})

const { emit, bus } = useEventsBus();

const getPosts = async (page = 1) => {
  return AdminDataService.getPosts(page)
    .then((res) => {
      dataContent.value = res.data.data.data;
      pagination.value = res.data.data.pagination;
    })
    .catch((error) => {
      console.log(error.response.data);
    });
};

let author = ref("")

let filters = {
    author: { value: author, keys: ["author"] },
};

getPosts();

async function deletePost(id) {
  return AdminDataService.deletePost(id)
    .then(() => {
      getPosts();
    })
    .catch((error) => {
      console.log(error.response.data);
    });
}

watch(
  () => bus.value.get('refreshPostsTable'),
  () => {
    getPosts();
  }
);
</script>

<template>
    <div class="m-auto mt-12 w-48 flex">
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
            sortKey="message"
            scope="col">Message</VTh>
            <VTh
            sortKey="likes"
            scope="col">Likes</VTh>
            <VTh
            sortKey="author"
            scope="col">Author</VTh>
            <th>Actions</th>
        </tr>
    </template>
    <template #body="{rows}">
        <tr v-for="object in rows" :key="object.id" class="border-2 border-orange-500">
            <td>{{ object.message }}</td>
            <td>{{ object.likes }}</td>
            <td>{{ object.author }}</td>
            <td>
                <button @click="emit('showEditPostModal', {user_id: object.id})" class="hover:text-white mr-2" title="Edit post">
                    <font-awesome-icon :icon="['fas', 'pen-to-square']" />
                </button>
                <button @click="deletePost(object.id)" class="hover:text-white" title="Delete post">
                    <font-awesome-icon :icon="['fas', 'trash']" />
                </button>
            </td>
        </tr>
    </template>
    </VTable>
    <TailwindPagination 
    class="justify-center text-white bg-transparent"
    :data="pagination"
    @pagination-change-page="getPosts"
    :limit="1"
    />
    <EditPostModal/>
</template>