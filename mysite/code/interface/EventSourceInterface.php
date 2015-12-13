<?php
interface EventSourceInterface {

    /**
     * Create a new event based data in this model
     */
    public function createNewEvent ($writeUpdate = true);

    /**
     * Update the linked event with data in this model
     */
    public function updateExistingEvent ($writeUpdate = true);
}
