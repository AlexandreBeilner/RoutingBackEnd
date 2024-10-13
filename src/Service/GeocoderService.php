<?php

namespace Routing\Service;

use Routing\Repository\AddressRepository;

class GeocoderService
{
    private mixed $googleAPIKEY;
    private AddressRepository $addressRepository;

    public function __construct()
    {
        $this->googleAPIKEY = $_ENV['GOOGLE_API_KEY'];
        $this->addressRepository = new AddressRepository();
    }

    public function getCoordinatesByAddress($userID): array | bool
    {
        $address = $this->addressRepository->getAddressByUserID($userID);
        if (empty($address[0])) {
            return false;
        }

        $addressFormated = urlencode($this->formatAddressToGoogleAPI($address[0]));
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$addressFormated}&key={$this->googleAPIKEY}";

        $response = file_get_contents($url);

        if ($response === FALSE) {
            return false;
        }

        $data = json_decode($response, true);

        if ($data['status'] == 'OK' && !empty($data['results'])) {
            $coordinates = $data['results'][0]['geometry']['location'];
            return ['latitude' => $coordinates['lat'], 'longitude' => $coordinates['lng']];
        }

        return false;
    }

    private function formatAddressToGoogleAPI($address): string
    {
        $formattedAddress = '';

        if (!empty($address['street'])) {
            $formattedAddress .= $address['street'];
        }

        if (!empty($address['number'])) {
            $formattedAddress .= ' ' . $address['number'];
        }

        if (!empty($address['neighborhood'])) {
            $formattedAddress .= ', ' . $address['neighborhood'];
        }

        if (!empty($address['city'])) {
            $formattedAddress .= ', ' . $address['city'];
        }

        if (!empty($address['state'])) {
            $formattedAddress .= ', ' . $address['state'];
        }

        if (!empty($address['zipcode'])) {
            $formattedAddress .= ', ' . $address['zipcode'];
        }

        return trim($formattedAddress, ', ');
    }

}