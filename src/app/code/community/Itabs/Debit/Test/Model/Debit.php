<?php

class Itabs_Debit_Test_Model_Debit extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test if you mask a string that you get the correct result
     *
     * @param array $data
     * @dataProvider dataProvider
     */
    public function testMaskString($data)
    {
        /* @var $model Itabs_Debit_Model_Debit */
        $model = Mage::getModel('debit/debit');

        // Load all expectations
        $dataSet = $this->readAttribute($this, 'dataName');

        for ($i = 0; $i < count($data); $i++) {
            $this->assertEquals(
                $this->expected($dataSet)->getData('string_'.$i),
                $model->maskString($data[$i])
            );
        }
    }
}
