<template>
  <!-- SPDX-FileCopyrightText: Andrei Zheksi <andrewsanser@gmail.com> SPDX-License-Identifier: AGPL-3.0-or-later
    -->
  <div id="content" class="app-mynotesapp">
    <NcAppNavigation>
      <NcAppNavigationNew
        v-if="!loading"
        :text="t('mynotesapp', 'New note')"
        :disabled="false"
        button-id="new-mynotesapp-button"
        button-class="icon-add"
        @click="newNote"
      />
      <ul>
        <div v-for="note in notes">
          <NcCounterBubble>42+</NcCounterBubble>
          <NcAppNavigationItem
            :key="note.id"
            :name="note.title ? note.title : t('mynotesapp', 'New note')"
            :class="{ active: currentNoteId === note.id }"
            @click="openNote(note)"
          >
            <template slot="actions">
                  <NcActionButton
                    v-if="note.id === -1"
                    icon="icon-close"
                    @click="cancelNewNote(note)"
                  >
                    {{ t("mynotesapp", "Cancel note creation") }}
                  </NcActionButton>            
              <!-- <div v-else> -->
              <NcActionButton v-else
                icon="icon-star"
                @click="deleteNote(note)">
                    {{ t("mynotesapp", "Add to favorites") }}
              </NcActionButton>

              <NcActionButton icon="icon-delete" @click="deleteNote(note)">
              {{ t("mynotesapp", "Delete note") }}
              </NcActionButton>

            </template>
          </NcAppNavigationItem>
        </div>
      </ul>
    </NcAppNavigation>
    <NcAppContent>
      <NoteForm  @saveNote="handleNoteSubmit"/>
      <p v-if="$store.state.sillyState">sillyState - false</p>
      <p v-else>sillyState - true</p>
      <!-- <NoteForm @saveNote="handleNoteSubmit"/> -->
    </NcAppContent>
  </div>
</template>

<script>
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton";
import NcAppContent from "@nextcloud/vue/dist/Components/NcAppContent";
import NcAppNavigation from "@nextcloud/vue/dist/Components/NcAppNavigation";
import NcAppNavigationItem from "@nextcloud/vue/dist/Components/NcAppNavigationItem";
import NcAppNavigationNew from "@nextcloud/vue/dist/Components/NcAppNavigationNew";
import NcCounterBubble from "@nextcloud/vue/dist/Components/NcCounterBubble"
import NoteForm from "./NoteForm.vue";

import "@nextcloud/dialogs/styles/toast.scss";
import { generateUrl } from "@nextcloud/router";
import { showError, showSuccess } from "@nextcloud/dialogs";
import axios from "@nextcloud/axios";

export default {
  name: "App",
  components: {
    NcActionButton,
    NcAppContent,
    NcAppNavigation,
    NcAppNavigationItem,
    NcAppNavigationNew,
    NcCounterBubble,
    NoteForm,
  },
  data() {
    return {
      notes: [], // notes => est réservée pour LE Composant. Il faut que la data soit partagées. Ça fait appel à la notion de "store".
      currentNoteId: null,
      // currentNote: {
      //   title: "Baptiste",
      //   content: "C'est ma note."
      // },
      updating: false,
      loading: true,
    };
  },
  computed: {
    /**
     * Return the currently selected note object
     * @returns {Object|null}
     */
    currentNote() {
      if (this.currentNoteId === null) {
        return null;
      }
      // this.currentNote = this.notes.find((note) => note.id === this.currentNoteId)
      return this.notes.find((note) => note.id === this.currentNoteId);
    },

    /**
     * Returns true if a note is selected and its title is not empty
     * @returns {Boolean}
     */
    savePossible() {
      return this.currentNote && this.currentNote.title !== "";
    },
  },
  /**
   * Fetch list of notes when the component is loaded
   */
  async mounted() {
    try {
      const response = await axios.get(generateUrl("/apps/mynotesapp/notes"));
      this.notes = response.data;
    } catch (e) {
      console.error(e);
      showError(t("notestutorial", "Could not fetch notes"));
    }
    this.loading = false;
  },

  methods: {
    /**
     * Create a new note and focus the note content field automatically
     * @param {Object} note Note object
     */
    openNote(note) {
      if (this.updating) {
        return;
      }
      this.currentNoteId = note.id;
      this.$nextTick(() => {
        this.$refs.content.focus();
      });
    },
    /**
     * Action tiggered when clicking the save button
     * create a new note or save
     */
    handleNoteSubmit() {
      if (this.currentNoteId === -1) {
        this.createNote(this.currentNote);
      } else {
        this.updateNote(this.currentNote);
      }
    },
    /**
     * Create a new note and focus the note content field automatically
     * The note is not yet saved, therefore an id of -1 is used until it
     * has been persisted in the backend
     */
    newNote() {
      if (this.currentNoteId !== -1) {
        this.currentNoteId = -1;
        this.notes.push({
          id: -1,
          title: "",
          content: "",
        });
        this.$nextTick(() => {
          this.$refs.title.focus();
        });
      }
    },
    /**
     * Abort creating a new note
     */
    cancelNewNote() {
      this.notes.splice(
        this.notes.findIndex((note) => note.id === -1),
        1
      );
      this.currentNoteId = null;
    },
    /**
     * Create a new note by sending the information to the server
     * @param {Object} note Note object
     */
    async createNote(note) {
      this.updating = true;
      try {
        const response = await axios.post(
          generateUrl("/apps/mynotesapp/notes"),
          note
        );
        const index = this.notes.findIndex(
          (match) => match.id === this.currentNoteId
        );
        this.$set(this.notes, index, response.data);
        this.currentNoteId = response.data.id;
      } catch (e) {
        console.error(e);
        showError(t("notestutorial", "Could not create the note"));
      }
      this.updating = false;
    },
    /**
     * Update an existing note on the server
     * @param {Object} note Note object
     */
    async updateNote(note) {
      this.updating = true;
      try {
        await axios.put(generateUrl(`/apps/mynotesapp/notes/${note.id}`), note);
      } catch (e) {
        console.error(e);
        showError(t("notestutorial", "Could not update the note"));
      }
      this.updating = false;
    },
    /**
     * Delete a note, remove it from the frontend and show a hint
     * @param {Object} note Note object
     */
    async deleteNote(note) {
      try {
        await axios.delete(generateUrl(`/apps/mynotesapp/notes/${note.id}`));
        this.notes.splice(this.notes.indexOf(note), 1);
        if (this.currentNoteId === note.id) {
          this.currentNoteId = null;
        }
        showSuccess(t("mynotesapp", "Note deleted"));
      } catch (e) {
        console.error(e);
        showError(t("mynotesapp", "Could not delete the note"));
      }
    },
  },
};
</script>
<style scoped>
#app-content > div {
  width: 100%;
  height: 100%;
  padding: 20px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}
</style>
