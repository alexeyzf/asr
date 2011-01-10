<?
/**
 * Model of cities_view view
 */

require_once('BaseModel.php');

class Cities extends BaseModel
{
    protected $_name = 'cities_view';
    protected $_primary = 'id';

    public function fetchAllEnabled()
    {
        return $this->fetchAll('is_enabled = 1')->toArray();
    }

    public function getEnabledCitiesOptions()
    {
        $citiesList = $this->fetchAllEnabled();
        $citiesOptions = array();

        foreach ($citiesList as $city)
        {
            $citiesOptions[$city['id']] = $city['name'];
        }

        return $citiesOptions;
    }
}