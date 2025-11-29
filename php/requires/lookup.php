<?php
function convertDatetimeToDate($datetime){
    $date = new DateTime($datetime);
  
    return $date->format('d/m/Y'); 
}

function searchCompanyById($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Company WHERE id=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchCompanyById2($value, $db) {
    $id = '';

    if(isset($value)){
        $decoded = json_decode($value, true);
        $searchValue = is_array($decoded) ? $decoded : $value;

        if (is_array($searchValue) && count($searchValue) > 0) {
            $placeholders = implode(',', array_fill(0, count($searchValue), '?'));
            $types = str_repeat('s', count($searchValue));

            if ($select_stmt = $db->prepare("SELECT * FROM Company WHERE id IN ($placeholders)")) {
                $select_stmt->bind_param($types, ...$searchValue);
                $select_stmt->execute();
                $result = $select_stmt->get_result();
                $names = [];
                while ($row = $result->fetch_assoc()) {
                    $names[] = $row['name'];
                }
                $id = implode('<br>', $names);
                $select_stmt->close();
            }
        } else {
            if ($select_stmt = $db->prepare("SELECT * FROM Company WHERE id=?")) {
                $select_stmt->bind_param('s', $searchValue);
                $select_stmt->execute();
                $result = $select_stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $id = $row['name'];
                }
                $select_stmt->close();
            }
        }
    }

    return $id;
}

function searchCompanyIdByName($value, $db) {
    $id = null;

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Company WHERE name=? and status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['id'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchCustomerCodeById($value, $db) {
    $id = null;

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Customer WHERE id=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['customer_code'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchCustomerNameById($value, $db) {
    $id = null;

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Customer WHERE id=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchPlantIdByName($value, $db) {
    $id = null;

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Plant WHERE name=? and status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['id'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchPlantCodeById($value, $db) {
    $id = '0';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Plant WHERE id=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['plant_code'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchPlantNameById($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Plant WHERE id=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchPlantNameByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Plant WHERE plant_code=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchProjectByCode($value, $db) {
    $id = '0';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Site WHERE site_code=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchProjectById($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Projects WHERE id=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['project'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchProductCodeById($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Product WHERE id=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['product_code'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchProductNameById($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Product WHERE id=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchTransporterNameByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Transporter WHERE transporter_code=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchAgentNameByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Agents WHERE agent_code=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchDestinationCodeByName($value, $db) {
    $id = '0';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Destination WHERE name=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['destination_code'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchFilePathById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM files WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['filepath'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchNamebyId($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM Users WHERE username=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['name'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchActionNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM Log_Action WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['description'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchTransportCapById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM Transport_Cap WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['transport_fit'].' - '.$row['transport_load'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchGateByLocationId($value, $db) {
    $id = '';

    if ($select_stmt = $db->prepare("SELECT * FROM Location WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['location_name'];
        }
        $select_stmt->close();
    }

    return $id;
}

function excelSerialToDate($serial) {
    // Excel date starts from 1900-01-01, subtract 1 for correct calculation
    $baseDate = strtotime('1899-12-30');
    return date('Y-m-d', strtotime("+$serial days", $baseDate));
}
?>