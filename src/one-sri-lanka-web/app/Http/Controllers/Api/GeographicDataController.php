<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Geographic Data Controller
 * Handles Sri Lankan administrative divisions data
 */
class GeographicDataController extends Controller
{
    /**
     * Get provinces data
     */
    public function provinces(Request $request): JsonResponse
    {
        $provinces = [
            [
                'id' => 1,
                'name' => 'Western Province',
                'name_si' => 'බස්නාහිර පළාත',
                'name_ta' => 'மேல் மாகாணம்',
                'code' => 'WP',
                'capital' => 'Colombo',
                'area_km2' => 3684,
                'population' => 5821710
            ],
            [
                'id' => 2,
                'name' => 'Central Province',
                'name_si' => 'මධ්‍යම පළාත',
                'name_ta' => 'மத்திய மாகாணம்',
                'code' => 'CP',
                'capital' => 'Kandy',
                'area_km2' => 5674,
                'population' => 2556497
            ],
            [
                'id' => 3,
                'name' => 'Southern Province',
                'name_si' => 'දකුණු පළාත',
                'name_ta' => 'தென் மாகாணம்',
                'code' => 'SP',
                'capital' => 'Galle',
                'area_km2' => 5544,
                'population' => 2465824
            ],
            [
                'id' => 4,
                'name' => 'Northern Province',
                'name_si' => 'උතුරු පළාත',
                'name_ta' => 'வட மாகாணம்',
                'code' => 'NP',
                'capital' => 'Jaffna',
                'area_km2' => 8884,
                'population' => 1061315
            ],
            [
                'id' => 5,
                'name' => 'Eastern Province',
                'name_si' => 'නැගෙනහිර පළාත',
                'name_ta' => 'கிழக்கு மாகாணம்',
                'code' => 'EP',
                'capital' => 'Trincomalee',
                'area_km2' => 9996,
                'population' => 1551381
            ],
            [
                'id' => 6,
                'name' => 'North Western Province',
                'name_si' => 'වයඹ පළාත',
                'name_ta' => 'வட மேல் மாகாணம்',
                'code' => 'NWP',
                'capital' => 'Kurunegala',
                'area_km2' => 7888,
                'population' => 2370075
            ],
            [
                'id' => 7,
                'name' => 'North Central Province',
                'name_si' => 'උතුරු මැද පළාත',
                'name_ta' => 'வட மத்திய மாகாணம்',
                'code' => 'NCP',
                'capital' => 'Anuradhapura',
                'area_km2' => 10714,
                'population' => 1266663
            ],
            [
                'id' => 8,
                'name' => 'Uva Province',
                'name_si' => 'ඌව පළාත',
                'name_ta' => 'ஊவா மாகாணம்',
                'code' => 'UP',
                'capital' => 'Badulla',
                'area_km2' => 8500,
                'population' => 1259421
            ],
            [
                'id' => 9,
                'name' => 'Sabaragamuwa Province',
                'name_si' => 'සබරගමුව පළාත',
                'name_ta' => 'சபரகமுவ மாகாணம்',
                'code' => 'SGP',
                'capital' => 'Ratnapura',
                'area_km2' => 4968,
                'population' => 1928655
            ]
        ];
        
        return response()->json([
            'success' => true,
            'data' => $provinces,
            'meta' => [
                'total' => count($provinces),
                'source' => 'Department of Census and Statistics'
            ]
        ]);
    }
    
    /**
     * Get districts data
     */
    public function districts(Request $request): JsonResponse
    {
        $province_id = $request->get('province');
        
        $districts = [
            // Western Province
            ['id' => 1, 'name' => 'Colombo', 'name_si' => 'කොළඹ', 'name_ta' => 'கொழும்பு', 'province_id' => 1, 'code' => 'CO'],
            ['id' => 2, 'name' => 'Gampaha', 'name_si' => 'ගම්පහ', 'name_ta' => 'கம்பஹா', 'province_id' => 1, 'code' => 'GA'],
            ['id' => 3, 'name' => 'Kalutara', 'name_si' => 'කළුතර', 'name_ta' => 'களுத்துறை', 'province_id' => 1, 'code' => 'KA'],
            
            // Central Province
            ['id' => 4, 'name' => 'Kandy', 'name_si' => 'මහනුවර', 'name_ta' => 'கண்டி', 'province_id' => 2, 'code' => 'KD'],
            ['id' => 5, 'name' => 'Matale', 'name_si' => 'මාතලේ', 'name_ta' => 'மாத்தளை', 'province_id' => 2, 'code' => 'MT'],
            ['id' => 6, 'name' => 'Nuwara Eliya', 'name_si' => 'නුවරඑළිය', 'name_ta' => 'நுவரேலியா', 'province_id' => 2, 'code' => 'NE'],
            
            // Southern Province
            ['id' => 7, 'name' => 'Galle', 'name_si' => 'ගාල්ල', 'name_ta' => 'காலி', 'province_id' => 3, 'code' => 'GL'],
            ['id' => 8, 'name' => 'Matara', 'name_si' => 'මාතර', 'name_ta' => 'மாத்தறை', 'province_id' => 3, 'code' => 'MR'],
            ['id' => 9, 'name' => 'Hambantota', 'name_si' => 'හම්බන්තොට', 'name_ta' => 'அம்பாந்தோட்டை', 'province_id' => 3, 'code' => 'HB'],
            
            // Northern Province
            ['id' => 10, 'name' => 'Jaffna', 'name_si' => 'යාපනය', 'name_ta' => 'யாழ்ப்பாணம்', 'province_id' => 4, 'code' => 'JA'],
            ['id' => 11, 'name' => 'Kilinochchi', 'name_si' => 'කිලිනොච්චි', 'name_ta' => 'கிளிநொச்சி', 'province_id' => 4, 'code' => 'KI'],
            ['id' => 12, 'name' => 'Mannar', 'name_si' => 'මන්නාරම', 'name_ta' => 'மன்னார்', 'province_id' => 4, 'code' => 'MA'],
            ['id' => 13, 'name' => 'Vavuniya', 'name_si' => 'වවුනියාව', 'name_ta' => 'வவுனியா', 'province_id' => 4, 'code' => 'VA'],
            ['id' => 14, 'name' => 'Mullaitivu', 'name_si' => 'මුලතිව්', 'name_ta' => 'முல்லைத்தீவு', 'province_id' => 4, 'code' => 'MU'],
            
            // Eastern Province
            ['id' => 15, 'name' => 'Batticaloa', 'name_si' => 'මඩකලපුව', 'name_ta' => 'மட்டக்களப்பு', 'province_id' => 5, 'code' => 'BC'],
            ['id' => 16, 'name' => 'Ampara', 'name_si' => 'අම්පාර', 'name_ta' => 'அம்பாறை', 'province_id' => 5, 'code' => 'AP'],
            ['id' => 17, 'name' => 'Trincomalee', 'name_si' => 'ත්‍රිකුණාමලය', 'name_ta' => 'திருகோணமலை', 'province_id' => 5, 'code' => 'TC'],
            
            // North Western Province
            ['id' => 18, 'name' => 'Kurunegala', 'name_si' => 'කුරුණෑගල', 'name_ta' => 'குருனாகல்', 'province_id' => 6, 'code' => 'KU'],
            ['id' => 19, 'name' => 'Puttalam', 'name_si' => 'පුත්තලම', 'name_ta' => 'புத்தளம்', 'province_id' => 6, 'code' => 'PU'],
            
            // North Central Province
            ['id' => 20, 'name' => 'Anuradhapura', 'name_si' => 'අනුරාධපුරය', 'name_ta' => 'அனுராதபுரம்', 'province_id' => 7, 'code' => 'AD'],
            ['id' => 21, 'name' => 'Polonnaruwa', 'name_si' => 'පොළොන්නරුව', 'name_ta' => 'பொலநறுவை', 'province_id' => 7, 'code' => 'PO'],
            
            // Uva Province
            ['id' => 22, 'name' => 'Badulla', 'name_si' => 'බදුල්ල', 'name_ta' => 'பதுளை', 'province_id' => 8, 'code' => 'BD'],
            ['id' => 23, 'name' => 'Moneragala', 'name_si' => 'මොණරාගල', 'name_ta' => 'மொணராகலை', 'province_id' => 8, 'code' => 'MG'],
            
            // Sabaragamuwa Province
            ['id' => 24, 'name' => 'Ratnapura', 'name_si' => 'රත්නපුර', 'name_ta' => 'இரத்தினபுரி', 'province_id' => 9, 'code' => 'RP'],
            ['id' => 25, 'name' => 'Kegalle', 'name_si' => 'කෑගල්ල', 'name_ta' => 'கேகாலை', 'province_id' => 9, 'code' => 'KE']
        ];
        
        // Filter by province if provided
        if ($province_id) {
            $districts = array_filter($districts, function($district) use ($province_id) {
                return $district['province_id'] == $province_id;
            });
        }
        
        return response()->json([
            'success' => true,
            'data' => array_values($districts),
            'meta' => [
                'total' => count($districts),
                'filters' => compact('province_id'),
                'source' => 'Department of Census and Statistics'
            ]
        ]);
    }
    
    /**
     * Get divisional secretariats data
     */
    public function divisionalSecretariats(Request $request): JsonResponse
    {
        $district_id = $request->get('district');
        
        // Sample DS divisions for major districts
        $dsData = [
            // Colombo District
            ['id' => 1, 'name' => 'Colombo', 'district_id' => 1, 'code' => 'CO01'],
            ['id' => 2, 'name' => 'Thimbirigasyaya', 'district_id' => 1, 'code' => 'CO02'],
            ['id' => 3, 'name' => 'Dehiwala-Mount Lavinia', 'district_id' => 1, 'code' => 'CO03'],
            ['id' => 4, 'name' => 'Moratuwa', 'district_id' => 1, 'code' => 'CO04'],
            ['id' => 5, 'name' => 'Sri Jayawardenepura Kotte', 'district_id' => 1, 'code' => 'CO05'],
            
            // Kandy District
            ['id' => 6, 'name' => 'Kandy', 'district_id' => 4, 'code' => 'KD01'],
            ['id' => 7, 'name' => 'Gampola', 'district_id' => 4, 'code' => 'KD02'],
            ['id' => 8, 'name' => 'Nawalapitiya', 'district_id' => 4, 'code' => 'KD03'],
            ['id' => 9, 'name' => 'Peradeniya', 'district_id' => 4, 'code' => 'KD04'],
            
            // Galle District
            ['id' => 10, 'name' => 'Galle', 'district_id' => 7, 'code' => 'GL01'],
            ['id' => 11, 'name' => 'Bentota', 'district_id' => 7, 'code' => 'GL02'],
            ['id' => 12, 'name' => 'Ambalangoda', 'district_id' => 7, 'code' => 'GL03'],
        ];
        
        // Filter by district if provided
        if ($district_id) {
            $dsData = array_filter($dsData, function($ds) use ($district_id) {
                return $ds['district_id'] == $district_id;
            });
        }
        
        return response()->json([
            'success' => true,
            'data' => array_values($dsData),
            'meta' => [
                'total' => count($dsData),
                'filters' => compact('district_id'),
                'source' => 'Department of Census and Statistics'
            ]
        ]);
    }
    
    /**
     * Get Grama Niladhari divisions data
     */
    public function gramaNiladhari(Request $request): JsonResponse
    {
        $ds_id = $request->get('divisional_secretariat');
        
        // Sample GN divisions
        $gnData = [
            // Colombo DS
            ['id' => 1, 'name' => 'Colombo Central', 'ds_id' => 1, 'code' => 'CO01/A'],
            ['id' => 2, 'name' => 'Fort', 'ds_id' => 1, 'code' => 'CO01/B'],
            ['id' => 3, 'name' => 'Slave Island', 'ds_id' => 1, 'code' => 'CO01/C'],
            ['id' => 4, 'name' => 'Cinnamon Gardens', 'ds_id' => 1, 'code' => 'CO01/D'],
            
            // Thimbirigasyaya DS
            ['id' => 5, 'name' => 'Bambalapitiya', 'ds_id' => 2, 'code' => 'CO02/A'],
            ['id' => 6, 'name' => 'Wellawatta', 'ds_id' => 2, 'code' => 'CO02/B'],
            ['id' => 7, 'name' => 'Kirulapone', 'ds_id' => 2, 'code' => 'CO02/C'],
            
            // Kandy DS
            ['id' => 8, 'name' => 'Kandy Four Gravets', 'ds_id' => 6, 'code' => 'KD01/A'],
            ['id' => 9, 'name' => 'Mahaiyawa', 'ds_id' => 6, 'code' => 'KD01/B'],
            ['id' => 10, 'name' => 'Ampitiya', 'ds_id' => 6, 'code' => 'KD01/C'],
        ];
        
        // Filter by DS if provided
        if ($ds_id) {
            $gnData = array_filter($gnData, function($gn) use ($ds_id) {
                return $gn['ds_id'] == $ds_id;
            });
        }
        
        return response()->json([
            'success' => true,
            'data' => array_values($gnData),
            'meta' => [
                'total' => count($gnData),
                'filters' => compact('ds_id'),
                'source' => 'Department of Census and Statistics'
            ]
        ]);
    }
}
