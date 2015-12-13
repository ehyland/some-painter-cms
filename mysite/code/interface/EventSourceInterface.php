<?php
interface EventSourceInterface {

    /**
     * Return Event and Gallery data
     */
    public function createModels ();

    /**
     * Create a new event based data in this model
     */
    public function createNewEvent ();

    /**
     * Update the linked event with data in this model
     */
    public function updateExistingEvent ();
}
