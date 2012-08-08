//
//  POIAnnotationView.h
//  WordPress
//
//  Created by Shakir Ali on 13/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import <MapKit/MapKit.h>

extern int const OOIANNOTATION_LABEL;
extern int const DETAIL_OoIMETA_BTN_TAG;

@interface OoIAnnotationView : MKPinAnnotationView
//@property (nonatomic, assign) MKMapView *map;
- (id)initWithAnnotation:(id <MKAnnotation>)annotation reuseIdentifier:annotationID;
@end
