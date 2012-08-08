//
//  POICircleOverlay.h
//  WordPress
//
//  Created by Shakir Ali on 13/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import <MapKit/MapKit.h>

@interface POICircleOverlay : MKCircle{
    int POI_ID;
}
@property int POI_ID;

@end
