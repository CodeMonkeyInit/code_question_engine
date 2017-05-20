<?php


class RunProgramDataContract extends BaseContract implements JsonSerializable
{

    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'firstName' => $this->firstname,
            'patronymic' => $this->patronymic,
            'lastName' => $this->lastname,
            'email' => $this->email,
            'active' => $this->active,
        );
    }
}